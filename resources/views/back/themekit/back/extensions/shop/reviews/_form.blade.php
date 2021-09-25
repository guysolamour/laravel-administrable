<div x-data="{show_create_fields: @json($form->getModel()->isGuest() ? 1 : 0)}">
    {!! form_start($form) !!}

  <div class="form-group">
      <label for="guest_author">Attribuer un auteur différent que vous ?</label>
      <select name="guest_author" id="guest_author" class="custom-select" x-model="show_create_fields">
            <option value="0">Non</option>
            <option value="1">Oui</option>
        </select>
  </div>

    <div x-show="show_create_fields == 1">
        {!! form_row($form->getField('name')) !!}
        {!! form_row($form->getField('email')) !!}
        {!! form_row($form->getField('phone_number')) !!}
    </div>

    {!! form_rest($form) !!}


    @if (isset($edit) && $edit)
    <div class="form-group">
        <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> Modifier</button>
    </div>
    @endif

    @if (!isset($edit))
    <div class="form-group">
        <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
    </div>
    @endif


    {!! form_end($form) !!}
</div>
