<div class="row p-0 m-0 mb-1">
    <div class="content">
        <div class="timeline timeline-start">
            <div class="timeline-container">
                @foreach ($customer_service->activity as $activity)
                    @include('system.customerservices.presential.edit.timeline.node')
                @endforeach
            </div>
        </div>
    </div>
</div>
