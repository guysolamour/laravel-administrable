@component('mail::message')
Bonjour <b>{{ $comment->getCommenterName() }}</b>, votre commentaire publié sur notre
plateforme vient de recevoir une réponse.

Merci, pour la confiance <br>
{{ config('app.name') }}
@endcomponent
