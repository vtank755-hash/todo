@extends('layouts.app')

@section('title', 'Create account · ' . config('app.name'))

@section('content')
<div class="auth-wrap">
    <div class="card auth-card">
        <h1 class="auth-title">Create your account</h1>
        <p class="auth-sub">We'll set up a few categories and example todos to get you going.</p>

        <form method="POST" action="{{ route('register.attempt') }}" class="stack">
            @csrf

            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       autocomplete="name" required autofocus>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password"
                       autocomplete="new-password" required>
                <small class="hint">At least 8 characters.</small>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>

        <p class="auth-alt">
            Already registered? <a href="{{ route('login') }}">Log in</a>
        </p>
    </div>
</div>
@endsection
