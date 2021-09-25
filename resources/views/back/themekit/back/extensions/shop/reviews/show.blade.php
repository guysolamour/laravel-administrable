@extends(back_view_path('layouts.base'))

@section('title', $review->getAuthorName())


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
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('extensions.shop.review.index') }}">Avis</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $review->getAuthorName() }}</a></li>
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
                            <a href="{{ back_route('extensions.shop.review.edit', $review) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>

                            @unless($review->approved)
                            <a href="{{ back_route('extensions.shop.review.approve',$review) }}" data-title="Confirmation" data-method="post"
                                data-confirm="Etes vous sûr de bien vouloir approuvé cet avis ?" class="btn btn-secondary" title="Approuvez"><i
                                    class="fas fa-check"></i></a>
                            @endunless
                            <a href="{{ back_route('extensions.shop.review.destroy', $review) }}" class="btn btn-danger"
                                data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>

                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-12">

                            <p class="pb-2"><b>Nom: </b>{{ $review->getAuthorName() }}</p>
                            <p class="pb-2"><b>Email: </b>{{ $review->getAuthorEmail() }}</p>
                            <p class="pb-2"><b>Numéro de téléphone: </b>{{ $review->getAuthorPhoneNumber() }}</p>
                            <p class="pb-2"><b>Message: </b>{{ $review->content }}</p>
                            <p class="pb-2"><b>En ligne: </b>{{ $review->approved ? 'oui' : 'non' }}</p>
                            <p class="pb-2"><b>Date ajout: </b>{{ $review->created_at->format('d/m/Y h:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
