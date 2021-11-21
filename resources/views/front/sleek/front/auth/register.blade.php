@extends('front.layouts.auth')


<x-administrable::seotags :force="true" title="Inscription" />

@section('content')
<div class="container d-flex flex-column justify-content-between vh-100">
    <div class="row justify-content-center mt-5">
        <div class="col-xl-5 col-lg-6 col-md-10">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="app-brand">
                        <a href="{{ route('home') }}">
                            <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"
                                width="30" height="33" viewBox="0 0 30 33">
                                <g fill="none" fill-rule="evenodd">
                                    <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z" />
                                    <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z" />
                                </g>
                            </svg>
                            <span class="brand-name">{{ config('app.name') }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-body p-5">
                    <h4 class="text-dark mb-5">Inscription</h4>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        @honeypot
                        <div class="row">
                            <div class="form-group col-md-12 mb-4">
                                <input type="text" name="name"
                                    class="form-control input-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" id="name" placeholder="Nom & prénoms">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-4">
                                <input type="text" name="phone_number"
                                    class="form-control input-lg @error('phone_number') is-invalid @enderror"
                                    id="phone_number" placeholder="Numéro de téléphone"
                                    value="{{ old('phone_number') }}">
                                @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 mb-4">
                                <input type="email" name="email"
                                    class="form-control input-lg @error('email') is-invalid @enderror" id="email"
                                    placeholder="Email" value="{{ old('email') }}">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>



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
                            <div class="form-group col-md-12 ">
                                <input type="password" name="password_confirmation" class="form-control input-lg"
                                    id="password_confirmation" placeholder="Confirmation mot de passe">
                            </div>
                            <div class="col-md-12">
                                {{-- <div class="d-inline-block mr-3">
                                    <label class="control control-checkbox">
                                        <input type="checkbox" />
                                        <div class="control-indicator"></div>
                                        J'accepte les conditions d'utilisations
                                    </label>

                                </div> --}}
                                <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">S'inscrire</button>
                                <p>Vous avez déjà un compte ?
                                    <a class="text-blue" href="{{ route('login') }}">Connectez vous</a>
                                </p>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
