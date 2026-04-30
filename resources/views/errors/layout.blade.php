@extends('layouts.app')

@section('head')
<title>Error — {{ config('app.name') }}</title>
<meta name="robots" content="noindex">
@endsection

@section('content')
<div class="text-center py-16">
    <h1 class="text-6xl font-bold text-gray-200 mb-4">@yield('code', '500')</h1>
    <p class="text-xl text-gray-600 mb-8">@yield('message', 'Something went wrong.')</p>
    <div class="flex gap-4 justify-center">
        <a href="{{ route('home') }}" class="px-6 py-3 bg-brand-500 text-white rounded-lg hover:bg-brand-600">Home</a>
        <a href="{{ route('bands.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Browse Bands</a>
    </div>
</div>
@endsection
