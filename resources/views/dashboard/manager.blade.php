@extends('layouts.app')

@section('content')

<h1>Dashboard</h1>

<p>Welcome back, {{ Auth::user()->name }} 👋</p>

<div class="cards">

    <div class="card">
        <div class="card-title">Opening Balance</div>
        <div class="card-value balance">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Today's Income</div>
        <div class="card-value income">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Today's Expense</div>
        <div class="card-value expense">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Closing Balance</div>
        <div class="card-value balance">₹0.00</div>
    </div>

</div>

<br>

<div class="card">

    <h2>Today's Account</h2>

    <p>
        No transactions recorded today.
    </p>

    <a href="{{ route('income.index') }}" class="btn">+ Add Income</a>

    <a href="{{ route('expenses.index') }}" class="btn">+ Add Expense</a>

</div>

@endsection