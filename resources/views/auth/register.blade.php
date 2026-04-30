@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Register', description: 'Create an account to favorite bands and leave comments.'); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-md mx-auto mt-8 mb-16">
    <div class="card card-compact bg-base-100 border border-base-300 shadow-sm">
        <div class="card-body p-6">
            <h1 class="card-title text-2xl justify-center mb-4">Create Account</h1>
            <form method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Name</span></label>
                    <input name="name" value="{{ old('name') }}" required class="input input-bordered w-full">
                    @error('name')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label"><span class="label-text">Email</span></label>
                    <input name="email" type="email" value="{{ old('email') }}" required class="input input-bordered w-full">
                    @error('email')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label"><span class="label-text">Password</span></label>
                    <input name="password" type="password" required minlength="8" class="input input-bordered w-full">
                    @error('password')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label"><span class="label-text">Confirm Password</span></label>
                    <input name="password_confirmation" type="password" required class="input input-bordered w-full">
                </div>
                <button type="submit" class="btn btn-primary w-full">Register</button>
                <p class="text-sm text-center text-base-content/50">Already have an account? <a href="{{ route('filament.admin.auth.login') }}" class="link link-primary">Log in</a></p>
            </form>
        </div>
    </div>
</div>
@endsection
