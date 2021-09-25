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
                                    <form action="{{ back_route('extensions.shop.settings.update') }}" method="post" x-data="{ show_tva: @json($settings->tva)}">
                                        @csrf
                                        @method('put')
                                        <div class="form-group">
                                            <label for="tva">Etes vous assujetis à la TVA</label>
                                            <select @change="show_tva = $event.target.value == 1 " name="tva" id="tva" class="custom-select">
                                                <option value="0" {{ !$settings->tva ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->tva ? 'selected' : ''  }}>Oui</option>
                                            </select>
                                            <small  class="form-text text-muted">
                                                Par défaut les prix mis lors de l'enregistrement d'un produit est affiché tel quel au client. Cepandant, vous
                                                pouvez mettre les prix HT et ajouter le taux de TVA qui sera automatiquement ajouté. Néanmoins, nous vous
                                                conseillons de mettre les prix TTC lors des enregistrement.
                                            </small>
                                        </div>
                                        <div class="form-group" x-show="show_tva">
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

                                        <div class="form-group">
                                            <label for="stock_management">Gestion de stock</label>
                                             <select name="stock_management" id="stock_management" class="custom-select">
                                                <option value="0" {{ $settings->stock_management ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->stock_management ? 'selected' : ''  }}>Oui</option>
                                            </select>

                                        </div>
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
                                          <div class="form-group">
                                            <label for="invoice_note">Note facture</label>
                                            <input type="text" id="invoice_note" class="form-control" name="invoice_note" value="{{ $settings->invoice_note }}">
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
                                        <div class="form-group">
                                            <label for="review">Activer les avis sur les produits</label>
                                            <select name="review" id="review" class="custom-select">
                                                <option value="0" {{ $settings->review ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->review ? 'selected' : ''  }}>Oui</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="note">Activer les notes sur les avis</label>
                                            <select name="note" id="note" class="custom-select">
                                                <option value="0" {{ $settings->note ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->note ? 'selected' : ''  }}>Oui</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="required_note">La note est elle obligatoire pour laisser un avis</label>
                                            <select name="required_note" id="required_note" class="custom-select">
                                                <option value="0" {{ $settings->required_note ? 'selected' : ''  }}>Non</option>
                                                <option value="1" {{ $settings->required_note ? 'selected' : ''  }}>Oui</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Enregistrer</button>
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
