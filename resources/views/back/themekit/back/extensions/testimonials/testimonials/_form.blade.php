{!! form_start($form) !!}

{!! form_rest($form) !!}


@if (isset($edit) && $edit)
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i>&nbsp; {{ Lang::get('administrable::messages.default.modify') }}</button>
</div>
@else
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i>&nbsp; {{ Lang::get('administrable::messages.default.save') }}</button>
</div>
@endif

{!! form_end($form) !!}

<x-administrable::tinymce
  selector="textarea[data-tinymce]"
  :model="$form->getModel()"
/>
