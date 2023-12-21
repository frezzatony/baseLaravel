<div class="timeline-row">
    <div class="timeline-icon">
        <img src="../../../assets/images/demo/users/face24.jpg" alt="">
    </div>
    <div class="card">
        <div class="card-header d-flex pa1 bg-light">
            <i class="ph-clock-counter-clockwise fs-sm mt-1 me-1 text-success"></i>
            <h5 class="fs-sm fw-normal mb-0 ">
                {{ \Carbon\Carbon::createFromTimestamp($activity->time)->format('d/m/Y') }} às
                {{ \Carbon\Carbon::createFromTimestamp($activity->time)->format('H:i:s') }}h
            </h5>
        </div>
        @include('system.customerservices.presential.edit.timeline.' . $activity->activity->reference . '.' . $activity->activity->action)
    </div>
</div>
