{!! form_start($form) !!}

{!! form_row($form->name) !!}

 <div class="row">
    <div class="col-md-6">
        {!! form_row($form->online) !!}
    </div>
    <div class="col-md-6">
        {!! form_row($form->type_id) !!}
    </div>
</div>
 <div class="row">
    <div class="col-md-6">
        {!! form_row($form->group_id) !!}
    </div>
    <div class="col-md-6">
        {!! form_row($form->link) !!}
    </div>
</div>
 <div class="row">
    <div class="col-md-6">
        {!! form_row($form->started_at) !!}
    </div>
    <div class="col-md-6">
        {!! form_row($form->ended_at) !!}
    </div>
</div>


{!! form_rest($form) !!}

<x-administrable::tinymce :model="$form->getModel()" />


@if (isset($edit) && $edit)
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> Modifier</button>
</div>
@else
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
</div>
@endif

{!! form_end($form) !!}

<x-administrable::select2 />

<x-administrable::daterangepicker
    fieldname="started_at"
    :model="$form->getModel()"
    :singledatepicker="true"
    opens="right"
    drops="bottom"
/>

<x-administrable::daterangepicker
    fieldname="ended_at"
    :model="$form->getModel()"
    :startdate="$form->getModel()->getKey() ? $form->getModel()->ended_at : now()->addMonth()"
    :singledatepicker="true"
    opens="right"
    drops="bottom"
/>
