@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Register', description: 'Create an account to favorite bands and leave comments.'); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4">
<div class="max-w-md mx-auto mt-8 mb-16">
    <div class="card bg-white dark:bg-ink-800 p-6">
        <h1 class="font-display text-2xl font-bold text-center mb-6 text-surface-900 dark:text-ink-200">Create Account</h1>
        <form method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Name</label>
                <input name="name" value="{{ old('name') }}" required class="input">
                @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required class="input">
                @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Password</label>
                <input name="password" type="password" required minlength="8" class="input">
                @error('password')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Confirm Password</label>
                <input name="password_confirmation" type="password" required class="input">
            </div>
            <button type="submit" class="btn btn-brand" style="width:100%">Register</button>
            <p class="text-xs text-center text-surface-400">
                Already have an account?
                <a href="{{ route('filament.admin.auth.login') }}" class="link font-semibold">Log in</a>
            </p>
        </form>
    </div>
</div>
</div>
@endsection
