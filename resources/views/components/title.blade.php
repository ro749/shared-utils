<title>{{ config('app.name', 'Laravel') }}</title>
@if(!empty(config('app.icon')))
<link rel="icon" href="{{ config('app.icon') }}" type="image/gif" sizes="16x16">
@else
<link rel="icon" href="data:,">
@endif