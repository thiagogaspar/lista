<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <style>
        body { font-family: 'DM Sans', system-ui, sans-serif; }
        .dark body, .dark { background: #0f0d0c; color: #e4e4e7; }
    </style>
</head>
<body class="bg-ink-50 dark:bg-ink text-surface-900 dark:text-ink-200 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <h1 class="font-display text-6xl font-bold text-brand-500 mb-4">@yield('code')</h1>
            <h2 class="font-display text-xl font-bold mb-2 text-surface-800 dark:text-ink-200">@yield('message')</h2>
            <p class="text-sm text-surface-500 dark:text-surface-400 mb-6">@yield('description')</p>
            <a href="{{ route('home') }}" class="btn btn-brand" aria-label="Go to home page">Back to Home</a>
        </div>
    </div>
</body>
</html>
