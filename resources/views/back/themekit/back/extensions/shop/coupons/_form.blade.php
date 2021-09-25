{!! form_start($form) !!}
@if(isset($edit) && $edit)
<div class="form-group">
{!! form_row($form->getField('code'), ['attr' => ['readonly' => true]]) !!}

@else
<div class="form-group" x-data="">
    <div class="input-group" >
        <div @click="document.querySelector('input[name=code]').value = Helper.randomString(10).toUpperCase()" class="input-group-prepend" style="cursor: pointer;">
            <div class="input-group-text">Générez un code</div>
        </div>
        {!! form_widget($form->getField('code'), ['attr' => ['placeholder' => 'Cliquer sur le bouton \'Générez un code\' juste à gauche pour générer un code automatiquement']]) !!}
    </div>
</div>
@endif
{!! form_row($form->getField('description')) !!}


<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
            aria-controls="nav-home" aria-selected="true">Général</a>
        <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
            aria-controls="nav-profile" aria-selected="false">Restrictions d'usage</a>
        <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab"
            aria-controls="nav-contact" aria-selected="false">Limite d'utilisation</a>
    </div>
</nav>
<div class="tab-content " style="padding: 10px;" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

        <div class="form-group">
            {!! form_widget($form->getField('remise_type')) !!}
            <small class="form-text text-muted">
                Pourcentage: appliquer une remise en pourcentage sur l'ensemble du panier <br>
                Panier fixe: appliquer une remise fixe sur l'ensemble du panier <br>
                Produit fixe: appliquer une remise fixe sur un produit du panier
                Produit pourcentage: appliquer une remise en pourcentage sur un produit du panier
            </small>

        </div>
        {!! form_row($form->getField('value')) !!}

        {!! form_row($form->getField('start_expire_dates')) !!}

    </div>
    <div class="tab-pane fade"  id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->getField('min_expense')) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->getField('max_expense')) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->getField('exclude_promo_products')) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->getField('use_once')) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->getField('products')) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->getField('exclude_products')) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! form_row($form->getField('categories')) !!}
            </div>
            <div class="col-md-6">
                {!! form_row($form->getField('exclude_categories')) !!}
            </div>
        </div>
        {{--  {!! form_row($form->getField('categories')) !!}  --}}
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        {!! form_row($form->getField('used_time_limit')) !!}
        {!! form_row($form->getField('used_by_user_limit')) !!}
    </div>
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

<x-administrable::select2 />

<x-administrable::daterangepicker
    fieldname='start_expire_dates'
    :startdate='$form->getModel()->starts_at'
    :enddate='$form->getModel()->expires_at'
    opens='right'
/>

@push('js')
<script src="{{ asset('js/vendor/helpers.js') }}"></script>
@endpush
