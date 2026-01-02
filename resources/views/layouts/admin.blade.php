<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - suite.in')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        body {
            background: #fafafa;
            color: #1a1a1a;
        }
    </style>
</head>
<body class="antialiased">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ Auth::check() ? route('admin.dashboard') : route('admin.login') }}" class="text-2xl font-bold text-gray-900">
                        suite.<span class="text-blue-600">in</span> <span class="text-sm text-gray-500">Admin</span>
                    </a>
                    @auth
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Dashboard</a>
                        <a href="{{ route('admin.hotels.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.hotels.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Hotels</a>
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">View Site</a>
                    </div>
                    @endauth
                </div>
                @auth
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', Auth::user()->role ?? 'user')) }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">Logout</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 max-w-7xl mx-auto mt-4">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 max-w-7xl mx-auto mt-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>
</html>

