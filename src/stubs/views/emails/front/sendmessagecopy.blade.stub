@component('mail::message')
Bonjour <b>{{ $message->name }}</b>, vous venez de nous contacter sur notre plateforme.
Ci-dessous le contenu de votre message. Nous vous reviendrons dans les plus brefs délais.


@component('mail::panel')
{{ $message->content }}
@endcomponent



Merci, pour la confiance <br>
{{ config('app.name') }}
@endcomponent
