<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>F-Taxi Accounts</title>

<style>

*{
    box-sizing:border-box;
}

html,
body{
    margin:0;
    padding:0;
    width:100%;
    min-height:100%;
}

body{
    font-family:Arial, sans-serif;
    background:#f5f7fb;
    color:#1f2937;
    overflow-x:hidden;
}

.layout{
    display:flex;
    min-height:100vh;
    width:100%;
}


/* =========================================================
   SIDEBAR
   ========================================================= */

.sidebar{
    width:240px;
    min-width:240px;
    background:#111827;
    color:white;
    padding:25px 15px;
}


/* LOGO */

.logo-box{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:0px;
}

.company-logo{
    width:160px;
    height:90px;
    object-fit:contain;
    display:block;
    opacity:1;
    filter:none;
    mix-blend-mode:normal;
}

.company-name{
    color:white;
    font-size:20px;
    font-weight:600;
    white-space:nowrap;
}


/* MENU */

.menu{
    margin-top:10px;
}

.menu a{
    display:block;
    color:#d1d5db;
    text-decoration:none;
    padding:13px 15px;
    border-radius:8px;
    margin-bottom:5px;
    font-size:16px;
    transition:
        background .2s ease,
        color .2s ease;
}

.menu a:hover{
    background:#374151;
    color:white;
}


/* ACTIVE MENU */

.menu a.active{
    background:#374151;
    color:white;
    font-weight:600;
}


/* =========================================================
   MAIN
   ========================================================= */

.main{
    flex:1;
    min-width:0;
    width:calc(100% - 240px);
}


/* =========================================================
   TOPBAR
   ========================================================= */

.topbar{
    height:70px;
    width:100%;
    background:white;

    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:0 30px;

    border-bottom:1px solid #e5e7eb;

    overflow:hidden;
}

.topbar-left{
    white-space:nowrap;
}

.topbar-right{
    display:flex;
    align-items:center;
    justify-content:flex-end;

    gap:12px;

    white-space:nowrap;

    flex-shrink:0;
}

.user-name{
    color:#111827;
    font-size:15px;
}


/* =========================================================
   LOGOUT
   ========================================================= */

.logout-form{
    display:inline-flex;
    align-items:center;

    margin:0;
    padding:0;
}

.logout-button{
    border:1px solid #dc2626;

    background:#dc2626;

    color:white;

    cursor:pointer;

    padding:7px 14px;

    margin:0;

    border-radius:6px;

    font-family:inherit;

    font-size:14px;

    line-height:1.2;
}

.logout-button:hover{
    background:#b91c1c;
    border-color:#b91c1c;
}


/* =========================================================
   CONTENT
   ========================================================= */

.content{
    padding:30px;

    min-width:0;

    overflow-x:auto;
}


/* =========================================================
   NOTIFICATIONS
   ========================================================= */

.alert{
    margin-bottom:20px;

    padding:14px 18px;

    border-radius:10px;

    font-size:14px;

    font-weight:600;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:15px;

    animation:
        slideDown .25s ease;
}

.alert-success{
    background:#ecfdf5;

    color:#166534;

    border:1px solid #bbf7d0;
}

.alert-error{
    background:#fef2f2;

    color:#991b1b;

    border:1px solid #fecaca;
}

.alert-close{
    border:none;

    background:transparent;

    color:inherit;

    font-size:20px;

    line-height:1;

    cursor:pointer;

    padding:0 2px;
}

.alert-close:hover{
    opacity:.7;
}

@keyframes slideDown{

    from{
        opacity:0;
        transform:translateY(-8px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}


/* =========================================================
   CARDS
   ========================================================= */

.cards{
    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:20px;
}

.card{
    background:white;

    padding:22px;

    border-radius:12px;

    box-shadow:
        0 2px 8px
        rgba(0,0,0,.05);
}

.card-title{
    color:#6b7280;
}

.card-value{
    font-size:25px;

    font-weight:bold;
}

.income{
    color:#15803d;
}

.expense{
    color:#dc2626;
}

.balance{
    color:#2563eb;
}


/* =========================================================
   BUTTON
   ========================================================= */

.btn{
    background:#111827;

    color:white;

    padding:10px 16px;

    border-radius:7px;

    text-decoration:none;

    border:none;

    cursor:pointer;
}


/* =========================================================
   TABLE
   ========================================================= */

table th,
table td{
    border-right:1px solid #ddd;
}

table th:last-child,
table td:last-child{
    border-right:none;
}


/* =========================================================
   MOBILE
   ========================================================= */

@media(max-width:900px){

    .sidebar{
        width:200px;
        min-width:200px;
    }

    .main{
        width:calc(100% - 200px);
    }

    .cards{
        grid-template-columns:
            repeat(2,1fr);
    }

    .topbar{
        padding:0 20px;
    }

}


@media(max-width:600px){

    .sidebar{
        width:180px;
        min-width:180px;

        padding:
            20px 10px;
    }

    .main{
        width:calc(100% - 180px);
    }

    .company-logo{
        width:130px;
        height:75px;
    }

    .company-name{
        font-size:18px;
    }

    .menu a{
        font-size:14px;
        padding:11px 10px;
    }

    .content{
        padding:20px 15px;
    }

    .topbar{
        padding:0 15px;
    }

    .user-name{
        display:none;
    }

}


/* =========================================================
   VERY SMALL SCREENS
   ========================================================= */

@media(max-width:450px){

    .sidebar{
        width:160px;
        min-width:160px;
    }

    .main{
        width:calc(100% - 160px);
    }

    .company-logo{
        width:115px;
        height:65px;
    }

    .company-name{
        font-size:16px;
    }

    .menu a{
        font-size:13px;
        padding:10px 8px;
    }

    .topbar{
        height:60px;
    }

}

</style>

</head>


<body>


<div class="layout">


<!-- =========================================================
     SIDEBAR
     ========================================================= -->

<aside class="sidebar">


    <!-- LOGO -->

    <div class="logo">

        <div class="logo-box">

            <img
                src="{{ asset('images/logo.png') }}"
                class="company-logo"
                alt="F-Taxi Logo"
            >

            <span class="company-name">
                F-Taxi
            </span>

        </div>

    </div>


    <!-- MENU -->

    <div class="menu">


        <!-- DASHBOARD -->

        <a
            href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            📊 Dashboard
        </a>


        <!-- DRIVERS -->


        <!-- INCOME -->

        <a
            href="{{ url('/income') }}"
            class="{{ request()->is('income*') ? 'active' : '' }}"
        >
            💰 Income
        </a>


        <!-- EXPENSES -->

        <a
            href="{{ url('/expenses') }}"
            class="{{ request()->is('expenses*') ? 'active' : '' }}"
        >
            💸 Expenses
        </a>


        <!-- REPORTS -->

        <a
            href="{{ url('/reports') }}"
            class="{{ request()->is('reports*') ? 'active' : '' }}"
        >
            📈 Reports
        </a>


        <!-- SEARCH -->

        <a
            href="{{ route('search.index') }}"
            class="{{ request()->is('search*') ? 'active' : '' }}"
        >
            🔍 Search
        </a>


    </div>


</aside>


<!-- =========================================================
     MAIN AREA
     ========================================================= -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">


        <div class="topbar-left">

            <strong>
                Account Management
            </strong>

        </div>


        <div class="topbar-right">


            @auth


                <span class="user-name">

                    {{ Auth::user()->name }}

                </span>


                <span>
                    |
                </span>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="logout-form"
                >

                    @csrf


                    <button
                        type="submit"
                        class="logout-button"
                    >
                        Logout
                    </button>

                </form>


            @else


                <a href="{{ route('login') }}">
                    Login
                </a>


            @endauth


        </div>


    </header>


    <!-- =====================================================
         CONTENT
         ===================================================== -->

    <section class="content">


        {{-- SUCCESS MESSAGE --}}

        @if(session('success'))

            <div class="alert alert-success">

                <span>

                    ✅ {{ session('success') }}

                </span>


                <button
                    type="button"
                    class="alert-close"
                    onclick="this.parentElement.remove()"
                    aria-label="Close"
                >
                    ×
                </button>

            </div>

        @endif


        {{-- ERROR MESSAGE --}}

        @if(session('error'))

            <div class="alert alert-error">

                <span>

                    ❌ {{ session('error') }}

                </span>


                <button
                    type="button"
                    class="alert-close"
                    onclick="this.parentElement.remove()"
                    aria-label="Close"
                >
                    ×
                </button>

            </div>

        @endif


        {{-- VALIDATION ERRORS --}}

        @if($errors->any())

            <div class="alert alert-error">


                <div>

                    @foreach($errors->all() as $error)

                        <div>
                            ❌ {{ $error }}
                        </div>

                    @endforeach

                </div>


                <button
                    type="button"
                    class="alert-close"
                    onclick="this.parentElement.remove()"
                    aria-label="Close"
                >
                    ×
                </button>


            </div>

        @endif


        {{-- PAGE CONTENT --}}

        @yield('content')


    </section>


</main>


</div>


</body>

</html>