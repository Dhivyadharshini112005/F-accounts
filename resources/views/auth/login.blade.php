<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>F-Taxi Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            width: 460px;
            background: white;
            padding: 25px 45px 45px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .logo-box {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-box img {
            width: 160px;
            height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 28px;
            color: #111827;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 16px;
            margin-bottom: 22px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .login-btn {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .error {
            color: #dc2626;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="login-box">

    <!-- F-Taxi Logo -->
    <div class="logo-box">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="F-Taxi Logo"
        >
    </div>

    <h2>Account Login</h2>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">

        @csrf

        <!-- Username -->
        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter your username"
            value="{{ old('username') }}"
            required
            autofocus
        >

        <!-- Password -->
        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <!-- Login Button -->
        <button
            type="submit"
            class="login-btn"
        >
            Login
        </button>

    </form>

</div>

</body>
</html>