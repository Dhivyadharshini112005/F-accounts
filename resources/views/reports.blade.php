<aside class="sidebar">

    <!-- COMPANY LOGO -->
    <div class="logo">

        <img src="{{ asset('images/logo.png') }}"
             class="company-logo"
             alt="F-TAXI Logo">

    </div>


    <!-- MENU -->
    <div class="menu">

        <a href="{{ route('dashboard') }}">
            📊 Dashboard
        </a>


        <a href="{{ route('drivers.index') }}">
            👥 Drivers
        </a>


        <a href="{{ route('income.index') }}">
            💰 Income
        </a>


        <a href="{{ route('expenses.index') }}">
            💸 Expenses
        </a>


        <a href="{{ route('daily.account') }}">
            📅 Daily Accounts
        </a>


        <a href="{{ route('reports.index') }}">
            📈 Reports
        </a>


    </div>

</aside>