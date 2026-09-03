@extends('layouts.shop')

@section('title', 'Регистрация')

@section('content')
    <h1 class="shop-h1">Регистрация</h1>
    <form method="post" action="{{ route('register') }}" class="shop-form" style="max-width: 28rem">
        @csrf
        <label>Имя
            <input type="text" name="name" value="{{ old('name') }}" required>
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>
        <label>Повтор пароля
            <input type="password" name="password_confirmation" required>
        </label>
        <button class="shop-btn" type="submit">Создать аккаунт</button>
    </form>
    @include('auth.partials.social')
    <p class="mt-4">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
@endsection
