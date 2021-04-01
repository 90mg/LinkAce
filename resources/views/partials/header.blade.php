<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ systemsettings('system_page_title') ?: config('app.name', 'LinkAce') }}</title>

@stack('html-header')
@include('partials.favicon')

@if($respectSystemColorScheme)
    <script>
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
@endif

<link href="{{ mix('assets/dist/css/app.css') }}" rel="stylesheet">

<script defer src="{{ mix('assets/dist/js/dependencies.js') }}"></script>
<script defer src="{{ mix('assets/dist/js/app.js') }}"></script>

<meta property="la-app-data" content="{{ json_encode([
    'user' => [
        'token' => csrf_token(),
    ],
    'routes' => [
        'fetch' => [
            'searchLists' => route('fetch-lists'),
            'searchTags' => route('fetch-tags'),
            'existingLinks' => route('fetch-existing-links'),
            'htmlForUrl' => route('fetch-html-for-url'),
            'updateCheck' => route('fetch-update-check'),
            'generateApiToken' => route('generate-api-token'),
            'generateCronToken' => route('generate-cron-token'),
        ]
    ]
]) }}">
