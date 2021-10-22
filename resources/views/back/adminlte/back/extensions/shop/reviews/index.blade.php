@extends(back_view_path('layouts.base'))


@section('title', 'Avis utilisateurs')



@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item active"><a href="#">Avis</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
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
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="list">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                id="check-all">
                                                <label class="custom-control-label"
                                                for="check-all"></label>
                                            </div>
                                        </th>
                                        <th>#</th>

                                        <th>Nom</th>
                                        <th>Produit</th>
                                        <th>Note</th>
                                        <th>Approuvé</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" data-check
                                                class="custom-control-input"
                                                data-id="{{ $review->id }}"
                                                id="check-{{ $review->id }}">
                                                <label class="custom-control-label"
                                                for="check-{{ $review->id }}"></label>
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
                <!-- /.card-body -->

                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>

        <x-administrable::datatable />
        @include(back_view_path('partials._deleteAll'))
        @endsection
