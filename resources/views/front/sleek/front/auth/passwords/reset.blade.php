@extends('front.layouts.auth')

<x-administrable::seotags :force="true" title="Réinitialisation de mot de passe" />

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

                    <h4 class="text-dark mb-5">Réinitialisation de mot de passe</h4>
                  

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @honeypot
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row">
                            <div class="form-group col-md-12 mb-4">
                                <input type="email" value="{{ $email ?? old('email') }}" name="email" readonly
                                    class="form-control input-lg @error('email') is-invalid @enderror" id="email"
                                    aria-describedby="emailHelp" placeholder="Email" required >
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 ">
                                <input type="password" name="password" class="form-control input-lg  @error('password') is-invalid @enderror"
                                    id="password" placeholder="Nouveau mot de passe">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 ">
                                <input type="password" name="password_confirmation" class="form-control input-lg" id="password_confirmation"
                                    placeholder="Confirmation nouveau mot de passe">
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection
