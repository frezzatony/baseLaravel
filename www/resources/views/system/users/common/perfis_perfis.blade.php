<textarea id="stored_profiles" class="d-none">
    {{ !empty($user->profiles) ? $user->profiles->toJson() : '' }}
</textarea>
<select id="profiles" name="profiles" multiple class=""></select>
