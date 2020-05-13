@extends('errors.master')


@section('title','La page a expiré')


@section('content')

<div class="container-tight py-6">
    <div class="empty">
        <div class="empty-icon">
            <div class="display-4">419</div>
        </div>
        <p class="empty-title h3">La page a expiré!</p>
        <p class="empty-subtitle text-muted">
            Désolé, votre session a expiré. Merci de rafraichir et réessayer.
        </p>
        <div class="empty-action">
            <a href="/" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <line x1="5" y1="12" x2="11" y2="18" />
                    <line x1="5" y1="12" x2="11" y2="6" /></svg>
                Retournez à l'accueil
            </a>
        </div>
    </div>
</div>

@endsection
