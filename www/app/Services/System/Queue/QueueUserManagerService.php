<?php

namespace App\Services\System\Queue;

use App\Models\System\Queue\QueueUserManager;
use Illuminate\Support\Facades\DB;
use App\Services\CrudService;
use Illuminate\Database\QueryException;

class QueueUserManagerService extends CrudService
{
    public static function updateQueueUsersManagers($queue, $managers)
    {
        DB::beginTransaction();

        if (empty($managers)) {
            try {
                QueueUserManager::where('queues_id', $queue->id)->each(function ($row, $key) {
                    $row->delete();
                });
                DB::commit();
                return [
                    'status'    =>  true,
                ];
            } catch (QueryException $e) {
                DB::rollBack();
                return false;
            }
        }

        $storedUsersManagers = QueueUserManager::where('queues_id', $queue->id)->get();
        foreach ($managers as $manager) {
            $storedUserManager = $storedUsersManagers->filter(function ($value, $key) use ($manager) {
                return $value->users_id == $manager['user_id'];
            });

            if ($storedUserManager->count() == 0) {
                try {
                    QueueUserManager::create([
                        'queues_id'     =>  $queue->id,
                        'users_id'      =>  $manager['user_id'],
                    ]);
                } catch (QueryException $e) {
                    DB::rollBack();
                    return false;
                }
            }
        }

        if ($storedUsersManagers->count() > sizeof($managers)) {
            foreach ($storedUsersManagers as $storedUserManager) {
                if (!in_array($storedUserManager->users_id, array_column($managers, 'user_id'))) {
                    try {
                        QueueUserManager::where([
                            'users_id'  =>  $storedUserManager->users_id,
                            'queues_id' =>  $queue->id,
                        ])->each(function ($row, $key) {
                            $row->delete();
                        });
                    } catch (QueryException $e) {
                        DB::rollBack();
                        return false;
                    }
                }
            }
        }

        DB::commit();
        return [
            'status'    =>  true,
        ];
    }
}
