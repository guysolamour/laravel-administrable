{!! form_start($form) !!}
<div class='row'>
    <div class='col-md-12'>
        {!! form_row($form->pseudo) !!}
    </div>
    <div class='col-md-6'>
        {!! form_row($form->first_name) !!}
    </div>
    <div class='col-md-6'>
        {!! form_row($form->last_name) !!}
    </div>
</div>
<div class='row'>
    <div class='col-md-6'>
        {!! form_row($form->email) !!}
    </div>
    <div class='col-md-6'>
        <label for="role">{{ Lang::get('administrable::messages.view.guard.role') }}</label>
        <select name="role" id="role" class="form-control select2" required>
            @php
                $roles = config('permission.models.role')::where('guard_name', config('administrable.guard'))->get();
            @endphp
            @foreach($roles  as $role)
                <option value="{{ $role->name }}" {{ get_guard()->hasRole($role, config('administrable.guard')) ? 'selected="selected"' : '' }}  >{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class='col-md-6'>
        {!! form_row($form->website) !!}
    </div>
    <div class='col-md-6'>
        {!! form_row($form->phone_number) !!}
    </div>

</div>
<div class='row'>

    <div class='col-md-4'>
        {!! form_row($form->facebook) !!}
    </div>
    <div class='col-md-4'>
        {!! form_row($form->twitter) !!}
    </div>
     <div class='col-md-4'>
        {!! form_row($form->linkedin) !!}
    </div>



</div>
<div class='row'>

    <div class='col-md-6'>
        {!! form_row($form->password) !!}
    </div>
    <div class='col-md-6'>
        {!! form_row($form->password_confirmation) !!}
    </div>

</div>
<div class='row'>
    <div class='col-md-12'>
        {!! form_row($form->about) !!}
    </div>
</div>
<div class='row'>
    <div class='col-md-12'>
        <button type='submit' class='btn btn-primary btn-block'> <i class='fas fa-plus'></i> {{ Lang::get('administrable::messages.default.add') }}</button>
    </div>
</div>
{!! form_end($form) !!}

