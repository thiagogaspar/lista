@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: __('common.auth.seo_title'), description: __('common.auth.seo_description')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="max-w-md mx-auto mt-8 mb-16">
    <div class="card bg-white dark:bg-ink-800 p-6">
        <h1 class="font-display text-2xl font-bold text-center mb-6 text-surface-900 dark:text-ink-200">{{ __('common.auth.create_account') }}</h1>
        <form method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reg-name" class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">{{ __('common.auth.name') }}</label>
                <input id="reg-name" name="name" value="{{ old('name') }}" required class="input">
                @error('name')<p class="text-[10px] text-red-500 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="reg-email" class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">{{ __('common.auth.email') }}</label>
                <input id="reg-email" name="email" type="email" value="{{ old('email') }}" required class="input">
                @error('email')<p class="text-[10px] text-red-500 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="reg-password" class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">{{ __('common.auth.password') }}</label>
                <input id="reg-password" name="password" type="password" required minlength="8" class="input">
                @error('password')<p class="text-[10px] text-red-500 mt-1" role="alert">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="reg-password-confirm" class="block text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">{{ __('common.auth.confirm_password') }}</label>
                <input id="reg-password-confirm" name="password_confirmation" type="password" required class="input">
            </div>
            <button type="submit" class="btn btn-brand" style="width:100%">{{ __('common.auth.register_button') }}</button>
            <p class="text-xs text-center text-surface-400">
                {{ __('common.auth.already_account') }}
                <a href="{{ route('filament.admin.auth.login') }}" class="link font-semibold">{{ __('common.auth.login') }}</a>
            </p>
        </form>
    </div>
</div>
</div>
@endsection
