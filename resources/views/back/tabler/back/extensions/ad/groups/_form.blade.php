{!! form_start($form) !!}

{!! form_row($form->name) !!}

<div class="row">
    <div class="col-md-4">
        {!! form_row($form->type_id) !!}
    </div>
    <div class="col-md-4">
        {!! form_row($form->visible_ads) !!}
    </div>
    <div class="col-md-4">
        {!! form_row($form->slider) !!}
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        {!! form_row($form->online) !!}
    </div>
    <div class="col-md-4">
        {!! form_row($form->width) !!}
    </div>
    <div class="col-md-4">
        {!! form_row($form->height) !!}
    </div>
</div>


{!! form_rest($form) !!}


@if (isset($edit) && $edit)
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> &nbsp;Modifier</button>
</div>
@else
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> &nbsp;Enregistrer</button>
</div>
@endif

{!! form_end($form) !!}

<x-administrable::select2 />
