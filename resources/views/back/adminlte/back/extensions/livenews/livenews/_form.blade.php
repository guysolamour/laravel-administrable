{!! form_start($form) !!}
    <div class="row">
        <div class="col-md-6">
            {!! form_row($form->text_color) !!}
        </div>
        <div class="col-md-6">
            {!! form_row($form->background_color) !!}
        </div>

        <div class="col-md-6">
           {!! form_row($form->uppercase) !!}
        </div>
        <div class="col-md-6">
           {!! form_row($form->online) !!}
        </div>
        <div class="col-md-4">
            {!! form_row($form->size) !!}
        </div>
        <div class="col-md-4">
            {!! form_row($form->started_at) !!}
        </div>
        <div class="col-md-4">
            {!! form_row($form->ended_at) !!}
        </div>
    </div>
    <div class="form-group">
        {!! form_row($form->content) !!}
    </div>

    <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.save') }}</button>
{!! form_end($form) !!}


<x-administrable::tinymce :model="$form->getModel()" />

<x-administrable::daterangepicker
  :model="$form->getModel()"
  fieldname="started_at"
  :singledatepicker="true"
  opens="right"
  drops="bottom"
/>

<x-administrable::daterangepicker
  :model="$form->getModel()"
  fieldname="ended_at"
  :startdate="$form->getModel()->ended_at ?: now()->addDay()"
  :singledatepicker="true"
  opens="right"
  drops="bottom"
/>
