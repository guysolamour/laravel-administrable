@extends(back_view_path('layouts.base'))

@section('title', $review->getAuthorName())

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                 <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>

                    <li class="breadcrumb-item">
                        <a href="{{ back_route('extensions.shop.review.index') }}">Avis</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $review->getAuthorName() }}</a></li>
                </ol>
            </ol>
        </nav>
    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Avis
            </h4> --}}

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.review.edit', $review) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editer</a>

                            @unless($review->approved)
                        <a href="{{ back_route('extensions.shop.review.approve',$review) }}" data-title="Confirmation" data-method="post"
                            data-confirm="Etes vous sûr de bien vouloir approuvé cet avis ?" class="btn btn-secondary" title="Approuvez"><i
                                class="fas fa-check"></i></a>
                        @endunless

                        <a href="{{ back_route('extensions.shop.review.destroy', $review) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                    </div>
                </div>
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
@endsection
