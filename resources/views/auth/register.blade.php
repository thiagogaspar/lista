@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Register', description: 'Create an account to favorite bands and leave comments.'); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-md mx-auto mt-8 mb-16">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-6">Create Account</h1>

    <form method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-1">Name</label>
            <input name="name" value="{{ old('name') }}" required
                   class="w-full px-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-1">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-1">Password</label>
            <input name="password" type="password" required minlength="8"
                   class="w-full px-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-1">Confirm Password</label>
            <input name="password_confirmation" type="password" required
                   class="w-full px-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
        </div>
        <button type="submit" class="w-full py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Register</button>
        <p class="text-sm text-surface-500 text-center">Already have an account? <a href="{{ route('filament.admin.auth.login') }}" class="text-brand-600 hover:underline">Log in</a></p>
    </form>
</div>
@endsection
