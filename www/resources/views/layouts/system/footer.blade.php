<textarea id="system_messages" class="d-none">
@if (session('messages'))
{{ json_encode(session('messages')) }}
@endif
</textarea>

<div class="navbar navbar-sm navbar-footer border-top">
    <div class="container-fluid">
        <span>&copy; {{ date('Y') }}</span>
    </div>
</div>
