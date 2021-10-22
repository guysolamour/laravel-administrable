@extends(back_view_path('layouts.base'))


@section('title', 'Avis utilisateurs')


@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active"><a href="#">Avis</a></li>
            </ol>

            <a href="{{ back_route('extensions.shop.review.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Attributs </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.review') }}" id="delete-all">
                <i class="fa fa-trash"></i> Tous supprimer
            </a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>
                <th>Nom</th>
                <th>Produit</th>
                <th>Note</th>
                <th>Approuvé</th>
                <th>Actions</th>

            </thead>
            <tbody>
                 @foreach($reviews as $review)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $review->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $review->id }}"
                                id="check-{{ $review->id }}"> <span class="form-check-label"></span>
                        </label>
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
                       <div class="btn-group" role="group">
                            <a href="{{ back_route('extensions.shop.review.show', $review) }}"
                                class="btn btn-primary" title="Affichez"><i class="fas fa-eye"></i></a>
                            @unless($review->approved)
                            <a href="{{ back_route('extensions.shop.review.approve', $review) }}"
                                data-title="Confirmation"
                                data-method="post"
                                data-confirm="Etes vous sûr de bien vouloir approuvé cet avis ?"
                                class="btn btn-secondary" title="Approuvez"><i
                                    class="fas fa-check"></i></a>
                            @endunless
                            <a href="{{ back_route('extensions.shop.review.edit', $review) }}"
                                class="btn btn-info" title="Editez"><i class="fas fa-edit"></i></a>

                            <a href="{{ back_route('extensions.shop.review.destroy', $review) }}"
                                data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                class="btn btn-danger" title="Supprimez"><i
                                    class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
