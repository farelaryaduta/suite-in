@extends('layouts.app')

@section('title', '429 - Too Many Requests')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-200">429</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Too Many Requests</h2>
        <p class="text-gray-600 mb-8">You have made too many requests. Please wait a moment before trying again.</p>
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Go Back Home
        </a>
    </div>
</div>
@endsection
