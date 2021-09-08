<div class="row">
    <div class="col-md-8">
        {!! form_start($form)!!}
        {!! form_rest($form)!!}
        <button type="submit" class="btn btn-secondary">Enregistrer</button>
        {!! form_end($form) !!}

    </div>
    <div class="col-md-4">
        @imagemanager([
            'collection'  => 'front-image',
            'label'       => 'Image à la une',
            'model'       => $form->getModel(),
            'type'        => 'image',
        ])
    </div>
</div>
