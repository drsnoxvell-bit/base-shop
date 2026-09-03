@extends('layouts.shop')

@section('title', 'Вход')

@section('content')
    <h1 class="shop-h1">Вход</h1>
    <form method="post" action="{{ route('login') }}" class="shop-form" style="max-width: 28rem">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>
        <label class="shop-check">
            <input type="checkbox" name="remember" value="1">
            Запомнить меня
        </label>
        <button class="shop-btn" type="submit">Войти</button>
    </form>
    @include('auth.partials.social')
    <p class="mt-4">Нет аккаунта? <a href="{{ route('register') }}">Регистрация</a></p>
@endsection
