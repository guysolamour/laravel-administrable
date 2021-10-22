@extends(back_view_path('layouts.base'))

@section('title', 'Avis')

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item active" aria-current="page">Avis</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Avis
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Avis
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.shop.review.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        <a href="#" data-model="{{ config('administrable.extensions.shop.models.review') }}" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> Tous Supprimer</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            <th>#</th>

                            <th>Nom</th>
                            <th>Produit</th>
                            <th>Note</th>
                            <th>Approuvé</th>
                            <th>Actions</th
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $review->id }}"
                                        id="check-{{ $review->id }}">
                                    <label class="form-check-label" for="check-{{ $review->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$review->getAuthorName() }}</td>
                            <td>
                                <a href="{{ back_route('extensions.shop.product.edit', $review->product) }}">{{ $review->product->name }}</a>
                            </td>
                            <td>{{$review->note }}</td>
                            <td>
                                @if ($review->approved)
                                    <i class="fa fa-check text-success"></i>
                                @else
                                    <i class="fa fa-times text-secondary"></i>
                                @endif
                            </td>
                            <td>
                               <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.shop.review.show', $review) }}"
                                        data-provide="tooltip" title="Afficher"><i
                                            class="ti-eye"></i></a>

                                    @unless($review->approved)
                                    <a href="{{ back_route('extensions.shop.review.approve', $review) }}"
                                        data-title="Confirmation"
                                        data-method="post"
                                        data-confirm="Etes vous sûr de bien vouloir approuvé cet avis ?"
                                        class="nav-link hover-primary" title="Approuvez"><i
                                            class="fas fa-check"></i></a>
                                    @endunless

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.shop.review.edit', $review) }}"
                                        data-provide="tooltip" title="Editer"><i
                                            class="ti-pencil"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.shop.review.destroy', $review) }}" data-provide="tooltip" title=""
                                        data-method="delete"
                                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                        title='Supprimer'
                                        data-original-title="Supprimer"><i class="ti-close"></i></a>
                                </nav>
                            </td>

                        </tr>

                       @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
