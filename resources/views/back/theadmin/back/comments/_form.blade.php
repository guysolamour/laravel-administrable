{!! form_start($form) !!}

{!! form_rest($form) !!}

<x-administrable::tinymce :model="$form->getModel()" />

@if (isset($edit) && $edit)
<div class="form-group">
    <button type="submit" class="btn btn-success">
        <i class="fa fa-edit"></i>
        {{ Lang::get('administrable::messages.default.edit') }}
    </button>
</div>
@else
<div class="form-group">
    <button type="submit" class="btn btn-success">
        <i class="fa fa-save"></i>
        {{ Lang::get('administrable::messages.default.save') }}
    </button>
</div>
@endif

{!! form_end($form) !!}
