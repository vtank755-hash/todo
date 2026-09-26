@extends('layouts.app')

@section('title', 'Log in · ' . config('app.name'))

@section('content')
<div class="auth-wrap">
    <div class="card auth-card">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-sub">Log in to pick up where you left off.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="stack">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password"
                       autocomplete="current-password" required>
            </div>

            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>

        <p class="auth-alt">
            No account yet? <a href="{{ route('register') }}">Create one</a>
        </p>
    </div>
</div>
@endsection
