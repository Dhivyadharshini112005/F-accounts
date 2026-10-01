@extends('layouts.app')

@section('content')

<h1>Owner Dashboard</h1>

<p>Welcome back, {{ Auth::user()->name }} 👋</p>

<div class="cards">

    <div class="card">
        <div class="card-title">Opening Balance</div>
        <div class="card-value balance">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Total Income</div>
        <div class="card-value income">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Total Expense</div>
        <div class="card-value expense">₹0.00</div>
    </div>

    <div class="card">
        <div class="card-title">Closing Balance</div>
        <div class="card-value balance">₹0.00</div>
    </div>

</div>

<br>

<div class="card">

    <h2>Account Management</h2>

    <p>Manage drivers, income, expenses and daily reports.</p>

    <a href="{{ route('drivers.index') }}" class="btn">Manage Drivers</a>

    <a href="{{ route('reports.index') }}" class="btn">View Reports</a>

</div>

@endsection