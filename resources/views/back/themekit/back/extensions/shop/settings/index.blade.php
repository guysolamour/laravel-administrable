@extends(back_view_path('layouts.base'))


@section('title', 'Réglages de la boutique')



@section('content')

<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route(config('administrable.guard') . '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">Réglages</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Modification des paramètres généraux de la boutique</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card-body p-0">
                                    <form action="{{ back_route('extensions.shop.settings.update') }}" method="post" x-data="settings">
                                        @csrf
                                        @method('put')
                                        <div class="form-group">
                                            <label for="tva">Etes vous assujetis à la TVA</label>
                                            <select  @change="form.tva = $event.target.value == 1 "  name="tva" id="tva" class="custom-select">
                                                <option value="0" :selected='!form.tva'>Non</option>
                                                <option value="1" :selected='form.tva'>Oui</option>
                                            </select>
                                            <small  class="form-text text-muted">
                                                Par défaut les prix mis lors de l'enregistrement d'un produit est affiché tel quel au client. Cepandant, vous
                                                pouvez mettre les prix HT et ajouter le taux de TVA qui sera automatiquement ajouté. Néanmoins, nous vous
                                                conseillons de mettre les prix TTC lors des enregistrement.
                                            </small>
                                        </div>
                                        <div class="form-group" x-show="form.tva" x-transition>
                                            <label for="tva_percentage">Taux TVA</label>
                                            <input type="text" id="tva_percentage" class="form-control" name="tva_percentage" value="{{ $settings->tva_percentage }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="command_prefix">Préfixe numéro de commande</label>
                                                    <input type="text" id="command_prefix" class="form-control" name="command_prefix" value="{{ $settings->command_prefix }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="command_length">Longeur de la commande</label>
                                                    <input type="text" id="command_length" class="form-control" name="command_length" value="{{ $settings->command_length }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="command_suffix">Suffixe numéro de commande</label>
                                                    <input type="text" id="command_suffix" class="form-control" name="command_suffix" value="{{ $settings->command_suffix }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <small  class="form-text text-muted">
                                                        Ces options sont utilisées pour la référence de chaque voiture. Vous pouvez laisser les
                                                        options par défaut à moins de savoir ce que vous faites.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stock_management">Gestion de stock</label>
                                                    <select name="stock_management" id="stock_management" class="custom-select">
                                                        <option value="0" {{ $settings->stock_management ? 'selected' : ''  }}>Non</option>
                                                        <option value="1" {{ $settings->stock_management ? 'selected' : ''  }}>Oui</option>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="invoice_management">Gestion de facture</label>
                                                    <select name="invoice_management" id="invoice_management" class="custom-select">
                                                        <option value="0" {{ $settings->invoice_management ? 'selected' : ''  }}>Non</option>
                                                        <option value="1" {{ $settings->invoice_management ? 'selected' : ''  }}>Oui</option>
                                                    </select>
                                                    <small  class="form-text text-muted">
                                                        Lors de validation de paiement de chaque commande, une facture est générée et envoyé au client
                                                        par mail. Si votre boutique ne fournit pas cette option, vous pouvez désactiver cette fonctionnalité
                                                        en choisissant l'option <mark>nom</mark>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="default_deliver_id">Choisir le livreur par défaut</label>
                                                    <select @change='chooseDefaultDeliver' x-model='form.default_deliver_id' name="default_deliver_id" id="default_deliver_id" class="custom-select">
                                                        <option value=""></option>
                                                        <template x-for='deliver in delivers'>
                                                            <option :value="deliver.id" x-text='deliver.name' :selected='deliver.id == form.default_deliver_id'></option>
                                                        </template>
                                                    </select>
                                                    <small  class="form-text text-muted">
                                                        Ce livreur sera utilisé en premier pour toutes les futures commandes. Le client pourra
                                                        à tout moment changer de livreur.
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col" x-show='form.default_deliver_id' x-transition>
                                                <div class="form-group">
                                                    <label for="default_coveragearea_id">Zone de livraison par défaut</label>
                                                    <select x-model='form.default_coveragearea_id' name="default_coveragearea_id" id="default_coveragearea_id" class="custom-select">
                                                        <option value=""></option>
                                                        <template x-for='area in coverageareas'>
                                                            <option :selected="area.id == form.default_coveragearea_id" :value="area.id" x-text='area.name + " - " + area.pivot.formated_price'></option>
                                                        </template>
                                                    </select>
                                                    <small  class="form-text text-muted" x-show='default_coveragearea_price' x-transition>
                                                        Cette zone sera utilisé pour calculer les frais de livraison pour un montant de
                                                        <mark x-text='default_coveragearea_price'></mark>.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="invoice_note">Note facture</label>
                                            <textarea type="text" id="invoice_note" class="form-control" name="invoice_note" >{{ $settings->invoice_note }}</textarea>
                                            <small  class="form-text text-muted">
                                                Au cas où, vous gérez les factures, vous pouvez ajouter une note au pied de page des factures.
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label for="coupon">Autoriser l'utilisation des codes promos</label>
                                            <select name="coupon" id="coupon" class="custom-select">
                                                <option value="0" {{ $settings->coupon ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->coupon ? 'selected' : ''  }}>Oui</option>
                                            </select>
                                        </div>
                                        <div class="row">

                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="review">Activer les avis sur les produits</label>
                                                    <select @change="form.review = $event.target.value == 1 " name="review" id="review" class="custom-select">
                                                        <option value="0" :selected='!form.review' >Non</option>
                                                        <option value="1" :selected='form.review' >Oui</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col" x-show='form.review' x-transition>
                                                <div class="form-group">
                                                    <label for="note">Activer les notes sur les avis</label>
                                                    <select  @change="form.note = $event.target.value == 1 " name="note" id="note" class="custom-select">
                                                        <option value="0" :selected='!form.note'>Non</option>
                                                        <option value="1" :selected='form.note'>Oui</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col" x-show='form.note' x-transition>
                                                <div class="form-group">
                                                    <label for="required_note">La note est elle obligatoire pour laisser un avis</label>
                                                    <select @change="form.required_note = $event.target.value == 1 " name="required_note" id="required_note" class="custom-select">
                                                        <option value="0" :selected='!form.required_note'>Non</option>
                                                        <option value="1" :selected='form.required_note'>Oui</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @foreach (config('administrable.extensions.shop.custom_fields.settings') ?? [] as $field)
                                            @if(Arr::get($field, 'type') === 'boolean')
                                                <div class="form-group">
                                                    <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label') }}</label>
                                                    <select name="custom_fields[{{  Arr::get($field, 'name') }}]" id="{{  Arr::get($field, 'name') }}" class="custom-select">
                                                        <option value="0" @if($settings->getCustomField(Arr::get($field, 'name')) == 0) selected @endif>Non</option>
                                                        <option value="1" @if($settings->getCustomField(Arr::get($field, 'name')) == 1) selected @endif>Oui</option>
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label') }}</label>
                                                    <input type="{{ Arr::get($field, 'type') }}" name="custom_fields[{{  Arr::get($field, 'name') }}]" class="form-control" value="{{ $edit_form->getModel()->getCustomField(Arr::get($field, 'name')) }}">
                                                </div>
                                            @endif
                                        @endforeach


                                        <button type="submit" class="btn btn-primary" :disabled="form.default_deliver_id && !form.default_coveragearea_id"><i class="fa fa-save"></i> Enregistrer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('settings', () => ({
            settings: @json($settings),
            delivers: @json($delivers),
            form: {},
            coverageareas: [],

            init(){
                this.form = {...this.settings}
                this.chooseDefaultDeliver()
            },

            chooseDefaultDeliver(){
                const deliver = this.delivers.filter(deliver => deliver.id == this.form.default_deliver_id)[0]

                if (!deliver){
                    return
                }

                this.coverageareas = deliver.areas
            },
            get default_coveragearea_price(){
                if (!(this.form.default_deliver_id && this.form.default_coveragearea_id)){
                    return
                }

                if (!this.coverageareas){
                    return
                }

                const coveragearea = this.coverageareas.filter(area => area.id == this.form.default_coveragearea_id)[0]

                if (!coveragearea){
                    return
                }

                return coveragearea.pivot.formated_price
            }
        }))
    })
</script>
@endpush
