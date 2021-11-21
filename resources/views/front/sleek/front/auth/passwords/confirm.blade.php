@extends('front.layouts.dashboard')

<x-administrable::seotags :force="true" title="Confirmation de mot de passe" />

@section('content')

<div class="content">
    <!-- Top Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success mb-4">
                <div class="card-header card-header-border-bottom">
                    <h2>Confirmation de mot de passe</h2>
                </div>
                <div class="card-body text-center">

                    <p class="font-weight-bold">
                       Merci de confirmer votre mot de passe avant de continuer <br>

                    </p>
                    <form action="{{ route('password.confirm') }}" method="POST">
                        @csrf
                        @honeypot
                        <div class="row mt-5">

                            <div class="form-group col-md-12 ">
                                <input type="password" name="password"
                                    class="form-control input-lg  @error('password') is-invalid @enderror" id="password"
                                    placeholder="Mot de passe">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>


                            <div class="col-md-12">
                                <button type="submit" class="btn btn-lg btn-primary  mb-4">
                                    Confirmer le mot de passe
                                </button>
                            </div>
                            <div class="col-md-12 text-center">
                                @if (Route::has('password.request'))
                                <p>Mot de passe oublié ? ?
                                    <a class="text-blue" href="{{ route('password.request') }}">Réinitialisez le ici</a>
                                </p>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
