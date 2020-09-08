<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

</head>

<body style="background-color: #f3f5f7; margin: 0 !important; padding: 0 !important;">

{{-- <!-- HIDDEN PREHEADER TEXT -->
<div
style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: &apos;Lato&apos;, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
Looks like you tried signing in a few too many times. Let's see if we can get you back into your account.
</div> --}}

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<!-- LOGO -->
<tr>
<td bgcolor="{{ config('administrable.notification_email_header_color', '#33cabb') }}" align="center">
<!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
{{ $header ?? '' }}

</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
<!-- HERO -->
<tr>
<td bgcolor="{{ config('administrable.notification_email_header_color', '#33cabb') }}" align="center" style="padding: 0px 10px 0px 10px;">
<!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

{{ $title ?? '' }}
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
<!-- COPY BLOCK -->
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
<!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<!-- COPY -->

<tr>
<td bgcolor="#ffffff" align="left"
style="padding: 20px 30px 40px 30px; color: #666666; font-family: &apos;Lato&apos;, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
<p style="margin: 0;">{{ Illuminate\Mail\Markdown::parse($slot) }}</p>



</td>
</tr>


</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
<!-- SUPPORT CALLOUT -->
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
<!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<!-- HEADLINE -->
<tr>
<td bgcolor="#fafafa" align="center"
style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: &apos;Lato&apos;, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
<h2 style="font-size: 16px; font-weight: 400; color: #111111; margin: 0;">Besoin d'aide?
</h2>
<p style="margin: 0; font-size: 14px;">
{{ configuration('about') }}
</p>
</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
<!-- FOOTER -->
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
<!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

{{ $subcopy ?? '' }}


{{ $footer ?? '' }}
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</td>
</tr>
</table>

</body>

</html>




















{{--


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html> --}}
