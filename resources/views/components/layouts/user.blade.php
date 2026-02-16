<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Content Management' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm" x-data="{ mobileMenu: false, profileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Left: Logo + Nav Links --}}
                <div class="flex items-center gap-8">
                    {{-- Logo --}}
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 flex-shrink-0">
                        <img src="{{ asset('images/tanbu.png') }}" alt="Tanah Bumbu" class="h-9">
                        <img src="{{ asset('images/beraksi.png') }}" alt="BerAKSI" class="h-6">
                    </a>

                    {{-- Desktop Nav --}}
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('user.dashboard') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
                            {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                            @if(request()->routeIs('user.dashboard')) style="background: linear-gradient(135deg, #2563EB, #1D4ED8);" @endif>
                            Dashboard
                        </a>
                        <a href="{{ route('user.contents.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150
                            {{ request()->routeIs('user.contents.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-100' }}"
                            @if(request()->routeIs('user.contents.*')) style="background: linear-gradient(135deg, #2563EB, #1D4ED8);" @endif>
                            Konten Saya
                        </a>
                    </div>
                </div>

                {{-- Right: Profile + Logout --}}
                <div class="flex items-center gap-3">
                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 leading-tight">{{ auth()->user()->instance?->name ?? 'User' }}</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.away="open = false" x-cloak x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-gray-200 shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Nav --}}
            <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-t border-gray-200 py-2">
                <a href="{{ route('user.dashboard') }}"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('user.dashboard') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:bg-gray-100' }}">
                    Dashboard
                </a>
                <a href="{{ route('user.contents.index') }}"
                    class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('user.contents.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-600 hover:bg-gray-100' }}">
                    Konten Saya
                </a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
