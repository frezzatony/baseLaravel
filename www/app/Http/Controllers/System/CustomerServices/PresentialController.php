<?php

namespace App\Http\Controllers\System\CustomerServices;

use App\Enums\PersonType;
use App\Enums\Queues\TicketSequence;
use App\Enums\Queues\TicketWithdrawal;
use App\Helpers\Crud\System\CustomerService\Forms\DefaultHelper;
use App\Helpers\CrudHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\CustomerServices\CustomerServicesCallRequest;
use App\Http\Requests\System\CustomerServices\CustomerServicesCreateRequest;
use App\Http\Requests\System\CustomerServices\CustomerServicesSaveRateRequest;
use App\Http\Requests\System\CustomerServices\CustomerServicesSaveRequest;
use App\Services\System\CustomerService\CustomerServicePresentialService;
use App\Services\System\CustomerService\CustomerServiceService;
use App\Services\System\Person\PersonService;
use App\Services\System\Queue\FirstComeManualBookService;
use App\Services\System\Queue\FirstComeManualService;
use App\Services\System\Queue\QueueService;
use App\Services\System\UserService;
use App\Services\System\WebsocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresentialController extends Controller
{
    public function index()
    {
        return view('system.customerservices.presential.index');
    }

    public function create(CustomerServicesCreateRequest $request)
    {
        $queue = QueueService::findById($request->input('queue'));
        $customerServiceResponse = CustomerServicePresentialService::create($queue, (int)$request->input('book'), Auth::user()->id);

        if (!$customerServiceResponse) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Não foi possível iniciar o atendimento.',
            ], 500);
        }

        if (!UserService::updateAttributes(Auth::user(), [
            'in_service'    =>  [
                'queue'             =>  $queue->id,
                'book'              =>  (int)$request->input('book'),
                'customer_service'  =>  $customerServiceResponse['customer_service']->customer_service_id,
            ]
        ])) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Não foi possível adotar o atendimento pelo usuário.',
            ], 500);
        }

        WebsocketService::message('queues.book', json_encode([
            'action'    =>  'assisting',
            'queue'     =>  $queue->id,
        ]));

        return view('system.customerservices.presential.edit', [
            'customer_service'  =>  $customerServiceResponse['customer_service'],
        ]);
    }

    public function edit(int $idQueue, int $idCustomerService)
    {
        $customerService = CustomerServicePresentialService::findByQueueIdAndId($idQueue, $idCustomerService);
        $customerService->form_data =  $customerService->form_data->all();
        $person = (int)$customerService->form_data['person_id'] ? PersonService::findById($customerService->form_data['person_id']) : null;

        return view('system.customerservices.presential.edit', [
            'form_structure'    =>  CrudHelper::getFormDataStructureWithValues(DefaultHelper::inputs(), $customerService->form_data),
            'customer_service'  =>  $customerService,
            'person'            =>  $person,
        ]);
    }

    public function update(CustomerServicesSaveRequest $request)
    {
        $customerService = CustomerServicePresentialService::findByQueueIdAndId($request->input('queue_id'), $request->get('id'));
        if (empty($customerService)) {
            abort(404);
        }

        $queue = QueueService::findById($request->input('queue_id'));
        $saveResponse = CustomerServicePresentialService::update($customerService, $queue, Auth::user()->id, $this->getSaveData($request));

        if ($saveResponse !== false && $request->input('conclude')) {
            if (!UserService::updateAttributes(Auth::user(), [
                'in_service'    =>  [
                    'queue'             =>  null,
                    'book'              =>  null,
                    'customer_service'  =>  null,
                ]
            ])) {
                return response()->json([
                    'status'    =>  'error',
                    'message'   =>  'Não foi possível encerrar o atendimento para o usuário.',
                ], 500);
            }

            WebsocketService::message('queues.book', json_encode([
                'action'    =>  'complete',
                'queue'     =>  $queue->id,
            ]));
        }

        if ($saveResponse == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == 'nothing_to_update') {
            return response([
                'status'    =>  'info',
                'id'        =>  $saveResponse['id'],
                'message'   =>  'Não há alterações para salvar.',
            ], 200);
        }

        if (($saveResponse['status'] ?? null) == 'updated') {
            return response([
                'status'                    =>  'success',
                'id'                        =>  $saveResponse['customer_service']->customer_service_id,
                'updated_at'                =>  $saveResponse['customer_service']->updated_at,
                'timeline_activity_html'    =>  view('system.customerservices.presential.edit.timeline.node', [
                    'form_structure'    =>  CrudHelper::getFormDataStructureWithValues(DefaultHelper::inputs(), json_decode($saveResponse['customer_service']->activity->first()->activity->values ?? '[]', true)),
                    'activity'          =>  $saveResponse['customer_service']->activity->first(),
                ])->render(),
                'person_id'                 => $saveResponse['person_id'] ?? 'null',
                'message'                   =>  'Item atualizado.',
            ], 200);
        }
    }

    public function updateRate(CustomerServicesSaveRateRequest $request)
    {
        $customerService = CustomerServicePresentialService::findByQueueIdAndId($request->input('queue_id'), $request->get('id'));
        if (empty($customerService)) {
            abort(404);
        }

        $queue = QueueService::findById($request->input('queue_id'));
        $saveResponse = CustomerServicePresentialService::rate($customerService, $queue, Auth::user()->id, $this->getSaveDataRate($request));

        if ($saveResponse == false) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Houve um erro ao executar a operação.',
            ]);
        }

        if (($saveResponse['status'] ?? null) == true) {
            return response()->json([
                'status'    =>  'success',
                'message'   =>  'Item atualizado.',
            ]);
        }
    }

    public function fetchProvideTicketScreen(Request $request)
    {
        $queue = (int)$request->input('queue') ? FirstComeManualService::findById($request->input('queue')) : null;
        if (empty($queue)) {
            abort(404);
        }

        if (in_array($queue->ticket_withdrawal, TicketWithdrawal::ATTENDANT_DISPENSER)) {
            if ($queue->ticket_sequence == TicketSequence::PRIORITY['value']) {
                return view('system.customerservices.presential.provideticket.attendant_ticket.priority_sequence', [
                    'tickets'   =>  FirstComeManualBookService::getBookTicketsByPrioritySequence($queue)
                ]);
            }
            if ($queue->ticket_sequence == TicketSequence::ISSUE['value']) {
                return view('system.customerservices.presential.provideticket.attendant_ticket.matter_sequence', [
                    'tickets'   =>  FirstComeManualBookService::getBookTicketsByMatterSequence($queue)
                ]);
            }
        }

        if (in_array($queue->ticket_withdrawal, TicketWithdrawal::DISPENSER)) {
            if ($queue->ticket_sequence == TicketSequence::PRIORITY['value']) {
                return view('system.customerservices.presential.provideticket.dispenser_ticket.priority_sequence', [
                    'tickets'   =>  FirstComeManualBookService::getBookTicketsByPrioritySequence($queue)
                ]);
            }
            if ($queue->ticket_sequence == TicketSequence::ISSUE['value']) {
                return view('system.customerservices.presential.provideticket.dispenser_ticket.matter_sequence', [
                    'tickets'   =>  FirstComeManualBookService::getBookTicketsByMatterSequence($queue)
                ]);
            }
        }
    }

    public function fetchBookTicket(Request $request)
    {
        $queue = (int)$request->input('queue') ? FirstComeManualService::findById($request->input('queue')) : null;
        if (empty($queue)) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Dados inválidos enviados.',
            ], 500);
        }

        $responseBook = FirstComeManualBookService::book($queue, $request->uuid_priority, $request->uuid_matter ?? null, $request->ticket);
        if ($responseBook != false && $responseBook['status'] == true) {
            WebsocketService::message('queues.book', json_encode([
                'action'    =>  'created',
                'queue'     =>  $queue->id,
            ]));
            return response()->json([
                'status'    =>  'success',
                'message'   =>  'Agendamento criado para senha: ' . $request->ticket,
            ], 200);
        }

        return response()->json([
            'status'    =>  'error',
            'message'   =>  'Não foi possível efetuar a retirada da senha.',
        ], 500);
    }

    public function fetchBookAndCallDispenserTicket(CustomerServicesCallRequest $request)
    {
        $queue = (int)$request->input('queue') ? FirstComeManualService::findById($request->input('queue')) : null;
        if (empty($queue)) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Dados inválidos enviados.',
            ], 500);
        }

        $responseBook = FirstComeManualBookService::book($queue, $request->uuid_priority, $request->uuid_matter ?? null, $request->ticket);
        if ($responseBook == false) {
            return response()->json([
                'status'    =>  'error',
                'message'   =>  'Não foi possível efetuar a chamada da senha.',
            ], 500);
        }

        $book = FirstComeManualBookService::callBook($queue, $responseBook['book']->id, Auth::user()->id, $request->input('service_point'));
        if ($book == false) {
            return response([
                'status'    =>  'error',
                'message'   =>  'Não foi possível efetuar a chamada da senha.',
            ], 400);
        }

        WebsocketService::message('queues.book', json_encode([
            'action'    =>  'calling',
            'queue'     =>  $queue->id,
        ]));

        return response([
            'status'    =>  'success',
            'book'      =>  $book['book'],
        ], 200);
    }

    public function attachments(Request $request)
    {
        $customerService = CustomerServiceService::findByQueueIdAndId($request->input('queue_id'), $request->input('crud_id'));
        if (empty($customerService)) {
            abort(404);
        }

        $attachmentCatalog = CustomerServiceService::getAttachmentsCatalog($customerService);

        if (!empty($attachmentCatalog->catalog)) {
            $response = $attachmentCatalog->{mb_strtolower($request->get('action'))}($request);
            if ($response !== false && is_array($response)) {
                return response($response, 200);
            }
            if ($response !== false && !is_array($response)) {
                return $response;
            }
        }
    }

    private function getSaveData(Request $request)
    {
        return [
            'conclude'          =>  $request->input('conclude'),
            'customer_service'  =>  $request->only([
                'comments_public', 'comments_internal',
            ]),
            'person'    =>  [
                'id'        =>  $request->input('person_id'),
                'person'    =>  [
                    'is_active'     =>  true,
                    'type'          =>  PersonType::NATURAL['value'],
                    'cpf_cnpj'      =>  $request->input('person_cpf'),
                    'name'          =>  $request->input('person_name'),
                    'social_name'   =>  $request->input('person_social_name'),
                ],
                'address'   =>   [
                    'cep'           =>  $request->input('person_address_cep'),
                    'state'         =>  $request->input('person_address_state'),
                    'city'          =>  $request->input('person_address_city'),
                    'neighborhood'  =>  $request->input('person_address_neighborhood'),
                    'street'        =>  $request->input('person_address_street'),
                    'number'        =>  $request->input('person_address_number'),
                    'complement'    =>  $request->input('person_address_complement'),
                    'regional_zone' =>  $request->input('person_address_regional_zone'),
                    'latitude'      =>  $request->input('person_address_latitude'),
                    'longitude'     =>  $request->input('person_address_longitude'),
                ],
                'contacts'  =>  $request->input('contact'),
            ]
        ];
    }

    private function getSaveDataRate(Request $request)
    {
        return [
            'customer_service'  => [
                'tags'                      =>  array_map('trim', explode(',', $request->input('tags'))),
                'problem_description'       =>  trim($request->input('problem_description')),
                'resolution_description'    =>  trim($request->input('resolution_description')),
            ]
        ];
    }
}
