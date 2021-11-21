@extends('front.layouts.dashboard')

<x-administrable::seotags :force="true" title="Vérification email" />

@section('content')

<div class="content">
    <!-- Top Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success mb-4">
                <div class="card-header card-header-border-bottom">
                    <h2>Vérification de votre email</h2>
                </div>
                <div class="card-body text-center">
                    @if (session('resent'))
                        <div class="alert alert-success font-weight-normal" role="alert">
                            Un nouveau lien de vérification vient de vous être envoyé par email.
                        </div>
                    @endif
                    <p class="font-weight-bold">
                        Avant de continuer, merci de vérifier votre boite de réception pour le lien de vérification
                        Si vous n'avez pas recu de mail, <br>
                        <form class="d-inline method=" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            @honeypot
                            <button type="submit" class="btn btn-success btn-rounded mt-3 font-weight-bold" align-baseline">Cliquer ici pour envoyer un
                                autre</button>.
                        </form>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
