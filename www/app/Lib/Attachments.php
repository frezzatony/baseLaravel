<?php

namespace App\Lib;

use App\Helpers\DBHelper;
use App\Models\AttachmentCatalog;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use ZipArchive;
use NcJoes\OfficeConverter\OfficeConverter;

class Attachments
{
    private const OFFICE_CONVERT_EXTENSIONS = [
        'xlsx'  =>  'ods',
        'xls'   =>  'ods',
        'csv'   =>  'ods',
        'docx'  =>  'odt',
        'doc'   =>  'odt',
        'ppt'   =>  'odp',
        'pptx'  =>  'odp',
    ];

    public function __construct($options = [])
    {
        $this->options = $options;
        if (App::runningUnitTests()) {
            return $this->test();
        }
        if (!empty($options['catalog_id'])) {
            $this->catalog = $this->findCatalogById($options['catalog_id'], $options['create_catalog_if_not_exists'] ?? false);
            if (!empty($this->catalog)) {
                $options['catalog_id'] = $this->catalog->id;
                $this->options['path'] .= '/' . $options['catalog_id'];
                $this->disk = $this->getCatalogStorageDriver($this->options['path']);
            }
        }
    }

    public function fetch($request)
    {
        return [
            'catalog'   =>  !empty($this->catalog) ? $this->catalog->catalog->toArray() : [],
        ];
    }

    public function create($request)
    {
        $attachment = $this->upload($request);
        if (!$attachment) {
            return false;
        }
        return $this->insert($attachment);
    }

    public function update($request)
    {
        $attachment = $this->upload($request);
        if (!$attachment) {
            return false;
        }

        $attachment['updated_at'] = date('Y-m-d H:i:s');
        $attachment['editor_id'] = auth()->user()->id;

        try {
            DB::beginTransaction();

            DB::table(AttachmentCatalog::getTableName())
                ->where('id', $this->catalog->id)
                ->update([
                    'catalog'       =>  DB::raw("jsonb_set(catalog,'{" . $attachment['key'] . "}','" . json_encode($attachment) . "')"),
                    'updated_at'    =>  'NOW()',
                ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        return $attachment;
    }

    public function thumbnail($request)
    {
        $attachment = $this->getAttachment($request);
        if (empty($attachment)) {
            return false;
        }
        return $this->disk->download("tmb/{$attachment['file']}");
    }

    public function preview($request)
    {
        $attachment = $this->getAttachment($request);
        if (empty($attachment['file']) || !$this->disk->exists($attachment['file'])) {
            return false;
        }

        if (strpos($attachment['mime'], 'image') !== false) {
            return $this->disk->download("{$attachment['file']}");
        }

        if (
            mb_strtolower($attachment['extension']) == 'pdf' ||
            in_array(mb_strtolower($attachment['extension']), array_merge(array_keys($this::OFFICE_CONVERT_EXTENSIONS), array_values($this::OFFICE_CONVERT_EXTENSIONS)))
        ) {
            return view('layouts.common.attachments.viewerjs', ['request' => $request, 'attachment' => $attachment])->render();
        }

        $request['id'] = [$request['id']];
        return $this->download($request);
    }

    public function download($request)
    {
        if (empty($request['id']) || !is_array($request['id'])) {
            return false;
        }

        $attachments = [];
        foreach ($request['id'] as $idAttachment) {
            $attachment = $attachment = $this->getAttachment([
                'id'    =>  $idAttachment
            ]);
            if (empty($attachment['file']) || !$this->disk->exists($attachment['file'])) {
                return false;
            }
            $attachments[] = $attachment;
        }
        return sizeof($attachments) == 1 ? $this->downloadFile($attachments, $request['convert_office'] ?? false) : $this->downloadZip($attachments);
    }

    public function delete($request)
    {
        $response = [
            'error_attachments' =>  [],
        ];
        $deleteDiskFiles = [];
        foreach ($request['id'] as $idAttachment) {
            $attachment = $attachment = $this->getAttachment([
                'id'    =>  $idAttachment
            ]);
            try {
                DB::beginTransaction();
                if (empty($attachment)) {
                    continue;
                }

                DB::table(AttachmentCatalog::getTableName())
                    ->where('id', $this->catalog->id)
                    ->update([
                        'catalog'       =>  DB::raw('catalog - ' . $attachment['key']),
                        'updated_at'    =>  'NOW()',
                    ]);
                $deleteDiskFiles[] = $attachment['file'];
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $response['error_attachments'][] = $idAttachment;
            }
        }

        foreach ($deleteDiskFiles as $deleteFile) {
            try {
                if ($this->disk->exists($deleteFile)) {
                    $this->disk->delete($deleteFile);
                }
                if ($this->disk->exists("{$deleteFile}.pdf")) {
                    $this->disk->delete("{$deleteFile}.pdf");
                }
                if ($this->disk->exists("tmb/{$deleteFile}")) {
                    $this->disk->delete("tmb/{$deleteFile}");
                }
            } catch (Exception $e) {
                DB::rollBack();
                $response['error_attachments'][] = $idAttachment;
            }
        }

        return $response;
    }

    public function deleteCatalog()
    {
        if (!$this->delete(['id' => array_column($this->catalog->catalog->toArray(), 'id'),])) {
            return false;
        }

        $canDelete = DBHelper::checkCanDeleteRow('public', AttachmentCatalog::getTableName(), $this->catalog->id);
        if ($canDelete !== true) {
            return [
                'status'    =>  false,
                'used_in'   =>  $canDelete['tables'],
            ];
        }

        try {
            DB::beginTransaction();
            AttachmentCatalog::where('id', $this->catalog->id)->delete();
            DB::commit();

            File::deleteDirectory($this->options['path']);

            return [
                'status'    =>  true,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'            =>  false,
                'console_message'   =>  $e,
            ];
        }
    }

    public function findCatalogById(int $idCatalog, $create = false)
    {
        $catalog = AttachmentCatalog::find($idCatalog);
        if (empty($catalog) && $create) {
            $catalog = $this->createCatalog($idCatalog);
        }
        if (!empty($catalog)) {
            $catalog->catalog = collect(json_decode($catalog->catalog));
        }
        return $catalog;
    }

    public static function createCatalog($idCatalog = null)
    {
        DB::beginTransaction();
        try {
            if ((int)$idCatalog) {
                $catalog = new AttachmentCatalog();
                $catalog->id = $idCatalog;
                $catalog->catalog = '[]';
                $catalog->save();
            }
            if (!(int)$idCatalog) {
                $catalog =  AttachmentCatalog::create([
                    'catalog'   =>  '[]',
                ]);
            }
            DB::commit();
            return $catalog;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private function upload($request)
    {
        $attachment = $this->getAttachment($request);
        $attachment = $this->diskUpload($attachment, $request);
        return $attachment ? $attachment : false;
    }

    private function insert($attachment)
    {
        try {
            DB::beginTransaction();
            DB::table(AttachmentCatalog::getTableName())
                ->where('id', $this->catalog->id)
                ->update([
                    'catalog'       =>  DB::raw('catalog || \'' . json_encode($attachment) . '\'::jsonb'),
                    'updated_at'    =>  'NOW()',
                ]);
            DB::commit();
            $this->catalog->catalog->merge([$attachment]);
            return $attachment;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private function getAttachment($request)
    {
        $key = $this->catalog->catalog->search(function ($i) use ($request) {
            return $i->id === $request['id'];
        });

        $attachment = $key !== false ? $this->catalog->catalog[$key] : null;
        if (empty($attachment)) {
            $extension = mb_strtolower($request['extension'] ?? null);
            $attachment = [
                'id'            =>  $request['id'],
                'extension'     =>  $extension,
                'name'          =>  $request['name'] ?? null,
                'created_at'    =>  date('Y-m-d H:i:s'),
                'updated_at'    =>  date('Y-m-d H:i:s'),
            ];
        } else {
            $attachment = (array)$attachment;
            $attachment['name'] = $request['name'] ?? $attachment['name'];
            $attachment['key'] = $key;
        }
        return $attachment;
    }

    private function diskUpload($attachment, $request)
    {
        $file = $request->file('filemanager-attachment-' . $request['id']);
        if (empty($file)) {
            return $attachment;
        }

        $fileName = $file->hashName();
        try {
            $this->disk->put($fileName, $file->get());
            if (!empty($attachment['file'])) {
                $this->disk->delete($attachment['file']);
                $attachment['previous_file'] = $attachment['file'];
            }
            $attachment['file'] = $fileName;
            $attachment['size'] = $file->getSize();
            $attachment['mime'] = $file->getMimeType();
            $attachment['editor_id'] = auth()->user()->id;
            if (!$this->createThumbnail($attachment)) {
                return false;
            }
            return $attachment;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createThumbnail($attachment)
    {
        if (strpos($attachment['mime'], 'image') !== false) {
            try {
                $file = $this->disk->get($attachment['file']);
                $thumb = Image::make($file);
                $thumb->fit(200);
                $this->disk->put("tmb/{$attachment['file']}", $thumb->encode('jpg', 60));
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    private function getCatalogStorageDriver($path)
    {
        return  Storage::build([
            'driver'        => 'local',
            'root'          =>  $path,
            'permissions'   => [
                'file'  => [
                    'public'    =>  0755,
                    'private'   =>  0755,
                ],
                'dir'   =>  [
                    'public'    =>  0755,
                    'private'   =>  0755,
                ],
            ],
        ]);
    }

    private function downloadFile($attachments, $convert = false)
    {
        if (
            $convert &&
            in_array(mb_strtolower($attachments[0]['extension']), array_keys($this::OFFICE_CONVERT_EXTENSIONS))
        ) {
            if (!$this->disk->exists($attachments[0]['file'] . '.pdf')) {
                $converter = new OfficeConverter($this->options['path'] . '/' . $attachments[0]['file']);
                $attachments[0]['file'] .= '.pdf';
                $converter->convertTo($attachments[0]['file']);
            } else {
                $attachments[0]['file'] .= '.pdf';
            }
        }

        if ($convert && strpos($this->disk->mimeType($attachments[0]['file']), 'plain') !== false) {
            return $this->disk->get($attachments[0]['file'], "{$attachments[0]['name']}.{$attachments[0]['extension']}");
        }

        return $this->disk->download($attachments[0]['file'], "{$attachments[0]['name']}.{$attachments[0]['extension']}");
    }

    private function downloadZip($attachments)
    {
        $fileName = 'anexos.zip';
        $zip = new ZipArchive();
        if ($zip->open($fileName, ZipArchive::CREATE) == TRUE) {
            foreach ($attachments as $attachment) {
                $zip->addFile($this->options['path'] . '/' . $attachment['file'], "{$attachment['name']}.{$attachment['extension']}");
            }
            $zip->close();
        }
        return response()->download($fileName)->deleteFileAfterSend(true);
    }

    private function test()
    {
        return true;
    }
}
