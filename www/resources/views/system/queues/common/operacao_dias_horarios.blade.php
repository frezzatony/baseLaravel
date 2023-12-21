<textarea id="stored_weekdays_hours" class="d-none">
    {{ !empty($queue->weekdays) 
        ? $queue->weekdays->map(function($weekday){return ['weekday'=>$weekday->weekday,'hours' => $weekday->hours];})->toJson() 
        : json_encode([[
                'weekday'   =>  'all',
                'hours'     =>[
                    ['uuid' => Str::uuid(), 'start' => '08:00', 'end' => '12:00'],
                    ['uuid' => Str::uuid(), 'start' => '13:00', 'end' => '17:00'],
                ]
        ]]) }}
</textarea>
<div class="row p-0 m-0 mb-1">
    <div class="col-md-9 py-0 px-1">
        <div class="list-group ">
            <div class="list-group-item bg-light fw-semibold fs-sm p-1">Dia da Semana</div>
            <div class="col-md-24 p-0 m-0 overflow-auto list-group-hours-weekday" style="height: 50vh;">
                @foreach (App\Enums\Weekday::asArray() as $key => $weekday)
                    <label class="list-group-item fs-sm p-1 text-break">
                        <input type="radio" class="me-1 list-group-radio-label" data-weekday="{!! $weekday['isodow'] !!}">
                        <span>{{ App\Helpers\StringHelper::nameComplete($weekday['label']) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-15 py-0 px-1">
        @foreach (App\Enums\Weekday::asArray() as $key => $weekday)
            <div class="list-group list-group-hours-weekday-hours list-group-hours-weekday-{!! $weekday['isodow'] !!} d-none">
                <div class="list-group-item bg-light fw-semibold fs-sm p-1">Horários de Atendimento - {{ App\Helpers\StringHelper::nameComplete($weekday['label']) }}</div>
                <div class="list-group-item p-1" >
                    <div class="row m-0 p-0 mb-2">
                        <div class="col-md-16 py-0 px-1">
                            <label for="operation[{!! $weekday['isodow'] !!}][availability]" class="form-label fw-semibold fs-sm m-0">Atendimentos {{in_array($key,['SUNDAY','SATURDAY']) ? 'aos' : 'às'}} {{ App\Helpers\StringHelper::nameComplete(implode('s-',explode('-',$weekday['label']))) }}s<span class="text-danger">*</span></label>
                            <select id="operation[{!! $weekday['isodow'] !!}][availability]" name="operation[{!! $weekday['isodow'] !!}][availability]" class="form-select px-1 pt1 pb1 fs-sm">
                                <option value="t" {{((!empty($queue->weekdays) && !empty($queue->weekdays->filter(function($storedWeekday) use($weekday){return $storedWeekday->weekday==$weekday['isodow'] && $storedWeekday->availability==true;})->toArray())) || (empty($queue->weekdays) && !in_array($key,['SUNDAY','SATURDAY']))) ? 'selected' : ''}}>SIM</option>
                                <option value="f" {{((!empty($queue->weekdays) && !empty($queue->weekdays->filter(function($storedWeekday) use($weekday){return $storedWeekday->weekday==$weekday['isodow'] && $storedWeekday->availability==false;})->toArray())) || (empty($queue->weekdays) && in_array($key,['SUNDAY','SATURDAY']))) ? 'selected' : ''}}>NÃO</option>
                            </select>
                        </div>
                    </div>
                    <div class="row p-0 m-0">
                        <fieldset class="mb-3 shadow px-1">
                            <legend class="fs-sm fw-bold border-bottom">
                                <div class="col-md-12 p-0">
                                    Horários de atendimento
                                </div>
                                <div class="col-md-12 p-0 d-flex justify-content-end m-0 mb-1 sticky-top bg-white">
                                    <button type="button" class="btn btn-secondary fs-sm px-1 py-0 btn-add-hour">
                                        <i class="ph-plus fs-sm"></i>Adicionar novo horário
                                    </button>
                                </div>
                            </legend>
                            <table id="tbl-appendgrid-queues-hours-weekday-{!! $weekday['isodow'] !!}" data-name="operation_{!!$weekday['isodow'] !!}_hours" class="table-scroll border hb-300 tbl-weekday-hours"></table>
                        </fieldset>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>