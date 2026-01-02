@extends('layouts.app')

@section('title', '419 - Session Expired')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-200">419</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Session Expired</h2>
        <p class="text-gray-600 mb-8">Your session has expired. Please refresh the page and try again.</p>
        <a href="{{ url()->previous() }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Refresh Page
        </a>
    </div>
</div>
@endsection
