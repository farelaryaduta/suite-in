@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-200">500</h1>
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Server Error</h2>
        <p class="text-gray-600 mb-8">Oops! Something went wrong on our end. Please try again later.</p>
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Go Back Home
        </a>
    </div>
</div>
@endsection
