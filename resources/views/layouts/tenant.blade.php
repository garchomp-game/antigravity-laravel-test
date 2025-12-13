<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OpsHub') }} - @yield('title', 'Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-base-200">
    <!-- Navbar -->
    <div class="navbar bg-base-100 shadow-lg">
        <div class="flex-1">
            <a href="{{ route('tenant.dashboard', ['tenant' => request()->route('tenant')]) }}" class="btn btn-ghost text-xl">
                🏢 OpsHub
            </a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a href="{{ route('tenant.dashboard', ['tenant' => request()->route('tenant')]) }}">Dashboard</a></li>
                <li><a href="{{ route('tenant.tickets.index', ['tenant' => request()->route('tenant')]) }}">Tickets</a></li>
                <li><a href="{{ route('tenant.admin.users.index', ['tenant' => request()->route('tenant')]) }}">Users</a></li>
                <li><a href="{{ route('tenant.audit.index', ['tenant' => request()->route('tenant')]) }}">Audit</a></li>
            </ul>
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                    <div class="bg-neutral text-neutral-content w-10 rounded-full">
                        <span>{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-10 mt-3 w-52 p-2 shadow">
                    <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto p-4">
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
