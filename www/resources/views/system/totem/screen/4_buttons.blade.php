<div id="screen-{{$id}}" class="col-md-24 screen screen-4-buttons {{ ($default ?? null) != true ? 'd-none ' : 'first' }}">
    <div class="col-md-24 text-center header">
        <h6 class="p-0 m-0 title-description">Bem vindo à Prefeitura de São Bento do Sul</h6>
        <h2 class="p-0 m-0 title">{!! $title ?? '&nbsp;' !!}</h2>
        <h4 class="p-0 m-0">{!! $subtitle ?? '&nbsp;' !!}</h4>
    </div>
    @php($count=1)
    @foreach($buttons as $button)
    <div class="col-md-12 col-sm-12 d-grid {{ $count%2==0 ? '' : null }}">
        <button type="button" class="btn btn-secondary btn-screen f3 px-0 btn-labeled {{ $count%2==0 ? 'btn-labeled-end' : 'btn-labeled-start'}}" data-target="{{ $button['target'] ?? null }}" data-book="{!! json_encode($button['book'] ?? null) !!}">
            <span class="btn-labeled-icon bg-black bg-opacity-20">
                <i class="{{ $count%2==0 ? 'ph-arrow-square-left' : 'ph-arrow-square-right'}}"></i>
            </span>
            {!! str_replace("\n","<br>",$button['title']) !!}
        </button>
    </div>
    @php($count++)        
   @endforeach

    <div class="col-md-24 m-0 ps-0 previous {{ ($default??null) == true ? 'd-none ' : '' }}">
        <div class="col-md-11 col-sm-11 ms-2 d-grid">
            <button type="button" class="btn btn-warning f3 px-0 btn-labeled btn-labeled-start btn-previous">
                <span class="btn-labeled-icon bg-black bg-opacity-20">
                    <i class="ph-arrow-fat-lines-left"></i>
                </span>
                VOLTAR
            </button>
        </div>
    </div>
</div>