@extends(back_view_path('layouts.base'))


@section('title', 'Avis')


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
                            <li class="breadcrumb-item active" aria-current="page">Avis</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Avis</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.review.create') }}" class="btn  btn-primary"> <i
                                    class="fa fa-plus"></i> Ajouter</a>



                            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.review') }}"
                                id="delete-all">
                                <i class="fa fa-trash"></i> Tous supprimer</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-vcenter card-table" id='list'>
                            <thead>
                                <th>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-all">
                                            <input type="checkbox" value="" id="check-all">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                        </label>
                                    </div>
                                </th>
                                <th>#</th>

                                <th>Nom</th>
                                <th>Produit</th>
                                <th>Note</th>
                                <th>Approuvé</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                <tr class="tr-shadow">
                                    <td>
                                        <div class="checkbox-fade fade-in-success ">
                                            <label for="check-{{ $review->getKey() }}">
                                                <input type="checkbox" data-check data-id="{{ $review->getKey() }}"
                                                    id="check-{{ $review->getKey() }}">
                                                <span class="cr">
                                                    <i class="cr-icon ik ik-check txt-success"></i>
                                                </span>
                                            </label>
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

                                            <a href="{{ back_route('extensions.shop.review.destroy',$review) }}"
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
            </div>

        </div>
    </div>
</div>

<x-administrable::datatable />
@include('back.partials._deleteAll')
@endsection
