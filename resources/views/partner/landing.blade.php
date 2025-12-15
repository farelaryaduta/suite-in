@extends('layouts.partner')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>

            <div class="relative pt-6 px-4 sm:px-6 lg:px-8"></div>

            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Maximize your hotel's</span>
                        <span class="block text-blue-600 xl:inline">revenue potential</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Join the fastest-growing hotel network. Get access to millions of travelers, powerful management tools, and 24/7 support to grow your business.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="{{ route('partner.register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg">
                                List Your Property
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-3">
                            <a href="#how-it-works" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1560&q=80" alt="Modern hotel room">
    </div>
</div>

<!-- Stats Section -->
<div class="bg-blue-900">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                Trusted by hoteliers worldwide
            </h2>
            <p class="mt-3 text-xl text-blue-200 sm:mt-4">
                Our numbers speak for themselves. Join a network that delivers results.
            </p>
        </div>
        <dl class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
            <div class="flex flex-col">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-blue-200">
                    Partner Hotels
                </dt>
                <dd class="order-1 text-5xl font-extrabold text-white">
                    10k+
                </dd>
            </div>
            <div class="flex flex-col mt-10 sm:mt-0">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-blue-200">
                    Monthly Guests
                </dt>
                <dd class="order-1 text-5xl font-extrabold text-white">
                    2M+
                </dd>
            </div>
            <div class="flex flex-col mt-10 sm:mt-0">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-blue-200">
                    Countries
                </dt>
                <dd class="order-1 text-5xl font-extrabold text-white">
                    15+
                </dd>
            </div>
        </dl>
    </div>
</div>

<!-- Benefits Section -->
<div id="benefits" class="py-16 bg-gray-50 overflow-hidden lg:py-24">
    <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
        <div class="relative">
            <h2 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Why partner with suite.in?
            </h2>
            <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                We provide the tools and exposure you need to take your hotel business to the next level.
            </p>
        </div>

        <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
            <div class="relative">
                <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                    Powerful Management Dashboard
                </h3>
                <p class="mt-3 text-lg text-gray-500">
                    Control your inventory, rates, and bookings from a single, easy-to-use interface. Get real-time insights into your performance.
                </p>

                <dl class="mt-10 space-y-10">
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Real-time Analytics</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Track your revenue, occupancy rates, and guest preferences with detailed reports.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Smart Pricing</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Optimize your room rates automatically based on demand and seasonality.
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
                <img class="relative mx-auto rounded-xl shadow-xl" width="490" src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Dashboard screenshot">
            </div>
        </div>
    </div>
</div>

<!-- How it Works Section -->
<div id="how-it-works" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
                How to get started
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                Three simple steps to list your property and start earning.
            </p>
        </div>

        <div class="mt-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mx-auto text-2xl font-bold">
                        1
                    </div>
                    <h3 class="mt-6 text-xl font-medium text-gray-900">Register</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Create your partner account in minutes. It's free and easy.
                    </p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mx-auto text-2xl font-bold">
                        2
                    </div>
                    <h3 class="mt-6 text-xl font-medium text-gray-900">Add Property</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Upload photos, set your rates, and add details about your hotel.
                    </p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 mx-auto text-2xl font-bold">
                        3
                    </div>
                    <h3 class="mt-6 text-xl font-medium text-gray-900">Go Live</h3>
                    <p class="mt-2 text-base text-gray-500">
                        Once verified, your hotel is live and ready to receive bookings.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-blue-600">
    <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
            <span class="block">Ready to grow your business?</span>
            <span class="block">Start your journey with suite.in today.</span>
        </h2>
        <p class="mt-4 text-lg leading-6 text-blue-100">
            Join thousands of successful hotel partners. No credit card required for registration.
        </p>
        <a href="{{ route('partner.register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
            Register Now for Free
        </a>
    </div>
</div>
@endsection
