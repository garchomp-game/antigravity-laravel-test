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
    <div class="sticky top-0 z-40 w-full backdrop-blur-md bg-base-100/90 border-b border-base-200 supports-[backdrop-filter]:bg-base-100/60">
        <div class="navbar container mx-auto px-4">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="{{ route('tenant.dashboard', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                        <li><a href="{{ route('tenant.tickets.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.tickets.*') ? 'active' : '' }}">Tickets</a></li>
                        <li><a href="{{ route('tenant.admin.users.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.admin.users.*') ? 'active' : '' }}">Users</a></li>
                        <li><a href="{{ route('tenant.audit.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.audit.*') ? 'active' : '' }}">Audit</a></li>
                    </ul>
                </div>
                <a href="{{ route('tenant.dashboard', ['tenant' => request()->route('tenant')]) }}" class="btn btn-ghost text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:bg-transparent">
                    OpsHub
                </a>
            </div>
            
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 gap-1">
                    <li><a href="{{ route('tenant.dashboard', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.dashboard') ? 'font-semibold text-primary' : 'hover:text-primary transition-colors' }}">Dashboard</a></li>
                    <li><a href="{{ route('tenant.tickets.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.tickets.*') ? 'font-semibold text-primary' : 'hover:text-primary transition-colors' }}">Tickets</a></li>
                    <li><a href="{{ route('tenant.admin.users.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.admin.users.*') ? 'font-semibold text-primary' : 'hover:text-primary transition-colors' }}">Users</a></li>
                    <li><a href="{{ route('tenant.audit.index', ['tenant' => request()->route('tenant')]) }}" class="{{ request()->routeIs('tenant.audit.*') ? 'font-semibold text-primary' : 'hover:text-primary transition-colors' }}">Audit</a></li>
                </ul>
            </div>
            
            <div class="navbar-end gap-3">
                <livewire:tenant-switcher :currentTenantSlug="request()->route('tenant')" />
                
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar ring ring-primary ring-offset-base-100 ring-offset-2 w-10 h-10">
                        <div class="bg-neutral text-neutral-content rounded-full w-10">
                            <span class="text-lg font-bold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                        </div>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[100] mt-3 w-52 p-2 shadow-lg border border-base-200">
                        <li class="menu-title px-4 py-2 text-xs text-base-content/50 uppercase font-bold tracking-wider">Account</li>
                        <li><a href="{{ route('profile.edit') }}" class="py-2">Profile</a></li>
                        <div class="divider my-1"></div>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 text-error">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
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
