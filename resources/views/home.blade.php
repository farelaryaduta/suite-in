@extends('layouts.app')

@section('title', 'suite.in - Temukan Hotel Impianmu')

@section('content')
<!-- Hero Section -->
<div class="relative bg-cover bg-center min-h-[600px]" style="background-image: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1920&q=80');">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg">
                Hai, mau ke mana? 👋
            </h1>
            <p class="text-xl md:text-2xl text-white/90 max-w-2xl mx-auto drop-shadow-md font-medium">
                Temukan hotel terbaik untuk liburan atau perjalanan bisnis Anda dengan harga terjangkau
            </p>
        </div>

        <!-- Search Box -->
        <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-8 max-w-5xl mx-auto backdrop-blur-sm bg-white/95">
            <form action="{{ route('home') }}" method="GET">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Location -->
                    <div class="flex-1 relative group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Kota Tujuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="city-search-input" name="city" value="{{ request('city') }}" placeholder="Mau nginep di mana?" autocomplete="off"
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-blue-500 focus:bg-white transition-all text-gray-800 font-medium placeholder-gray-400 group-hover:border-blue-200">
                            <div id="search-results" class="absolute w-full bg-white mt-2 rounded-xl shadow-xl border border-gray-100 hidden z-50 overflow-hidden max-h-80 overflow-y-auto"></div>
                        </div>
                    </div>

                    <!-- Check In -->
                    <div class="flex-1 group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Check In</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" name="check_in" value="{{ request('check_in', date('Y-m-d')) }}" 
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-blue-500 focus:bg-white transition-all text-gray-800 font-medium group-hover:border-blue-200">
                        </div>
                    </div>

                    <!-- Check Out -->
                    <div class="flex-1 group">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Check Out</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" name="check_out" value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}" 
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-0 focus:border-blue-500 focus:bg-white transition-all text-gray-800 font-medium group-hover:border-blue-200">
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full lg:w-auto bg-yellow-400 text-blue-900 px-10 py-4 rounded-2xl font-bold hover:bg-yellow-300 transition-all shadow-lg shadow-yellow-400/30 flex items-center justify-center space-x-2 transform hover:-translate-y-0.5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Cari Hotel</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="flex flex-wrap justify-center gap-4 md:gap-8 mt-12">
            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md px-6 py-3 rounded-full border border-white/20">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-white font-semibold">{{ $hotels->total() }}+ Hotel Pilihan</span>
            </div>
            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md px-6 py-3 rounded-full border border-white/20">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                </svg>
                <span class="text-white font-semibold">Lokasi Strategis</span>
            </div>
            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md px-6 py-3 rounded-full border border-white/20">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="text-white font-semibold">Jaminan Harga Terbaik</span>
            </div>
        </div>
    </div>
</div>

<!-- Promo Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10 mb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Promo 1 -->
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl p-6 text-white shadow-xl transform hover:-translate-y-1 transition-all cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">HOT DEAL</span>
                    <h3 class="text-2xl font-bold mt-3 mb-1">Diskon 50%</h3>
                    <p class="text-white/90 text-sm mb-4">Untuk pengguna baru</p>
                    <button class="bg-white text-rose-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-rose-50 transition-colors">Ambil Promo</button>
                </div>
                <div class="text-6xl opacity-20">🏷️</div>
            </div>
        </div>

        <!-- Promo 2 -->
        <div class="bg-gradient-to-r from-violet-500 to-purple-500 rounded-2xl p-6 text-white shadow-xl transform hover:-translate-y-1 transition-all cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">LIBURAN</span>
                    <h3 class="text-2xl font-bold mt-3 mb-1">Staycation Seru</h3>
                    <p class="text-white/90 text-sm mb-4">Cashback s.d 100rb</p>
                    <button class="bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-purple-50 transition-colors">Lihat Detail</button>
                </div>
                <div class="text-6xl opacity-20">🏖️</div>
            </div>
        </div>

        <!-- Promo 3 -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl p-6 text-white shadow-xl transform hover:-translate-y-1 transition-all cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold backdrop-blur-sm">BUSINESS</span>
                    <h3 class="text-2xl font-bold mt-3 mb-1">Perjalanan Bisnis</h3>
                    <p class="text-white/90 text-sm mb-4">Harga khusus korporat</p>
                    <button class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors">Cek Info</button>
                </div>
                <div class="text-6xl opacity-20">💼</div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Destinations -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Destinasi Populer</h2>
    <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
        @php
            $destinations = [
                ['name' => 'Jakarta', 'image' => 'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?q=80&w=300&auto=format&fit=crop'],
                ['name' => 'Bali', 'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=300&auto=format&fit=crop'],
                ['name' => 'Bandung', 'image' => 'https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=300&auto=format&fit=crop'],
                ['name' => 'Yogyakarta', 'image' => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?q=80&w=300&auto=format&fit=crop'],
                ['name' => 'Surabaya', 'image' => 'https://images.unsplash.com/photo-1712617645164-47a89a71fd04?q=80&w=300&auto=format&fit=crop'],
                ['name' => 'Semarang', 'image' => 'https://images.unsplash.com/photo-1625967445189-d35d49a37501?q=80&w=300&auto=format&fit=crop'],
            ];
        @endphp
        @foreach($destinations as $dest)
        <a href="{{ route('home', ['city' => $dest['name']]) }}" class="flex-shrink-0 group cursor-pointer destination-link" data-city="{{ $dest['name'] }}">
            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg group-hover:shadow-xl transition-all group-hover:scale-105 relative">
                <img src="{{ $dest['image'] }}" alt="{{ $dest['name'] }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
            </div>
            <p class="text-center mt-3 font-semibold text-gray-700 group-hover:text-blue-600">{{ $dest['name'] }}</p>
        </a>
        @endforeach
    </div>
</div>

<!-- Hotel Listing Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-gray-50" id="hotel-list-container">
    @include('partials.hotel-list')
</div>
</div>

<!-- Why Choose Us Section -->
<div class="bg-gradient-to-b from-gray-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Kenapa Pilih suite.in?</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">Kami berkomitmen memberikan pengalaman booking hotel terbaik untuk Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Harga Terbaik</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Dapatkan harga terbaik untuk setiap pemesanan hotel. Kami jamin harga kompetitif!</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Booking Aman</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Transaksi dijamin aman dengan sistem pembayaran terenkripsi dan terpercaya.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Support 24/7</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Tim customer service kami siap membantu Anda kapan saja, 24 jam sehari.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const destinationLinks = document.querySelectorAll('.destination-link');
        const cityInput = document.querySelector('input[name="city"]');
        const hotelListContainer = document.getElementById('hotel-list-container');

        destinationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const city = this.getAttribute('data-city');
                
                // Update input
                if(cityInput) cityInput.value = city;

                // Update URL
                const url = new URL(window.location.href);
                url.searchParams.set('city', city);
                window.history.pushState({}, '', url);

                // Show loading state
                hotelListContainer.style.opacity = '0.5';

                // Fetch results
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    hotelListContainer.innerHTML = html;
                    hotelListContainer.style.opacity = '1';
                    // Scroll to results with offset for sticky navbar
                    const yOffset = -100; 
                    const y = hotelListContainer.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({top: y, behavior: 'smooth'});
                })
                .catch(error => {
                    console.error('Error:', error);
                    hotelListContainer.style.opacity = '1';
                });
            });
        });

        // Event delegation for reset button
        hotelListContainer.addEventListener('click', function(e) {
            const resetBtn = e.target.closest('.reset-filter-btn');
            if (resetBtn) {
                e.preventDefault();
                
                // Clear input
                if(cityInput) cityInput.value = '';

                // Update URL
                const url = new URL(window.location.href);
                url.searchParams.delete('city');
                window.history.pushState({}, '', url);

                // Show loading state
                hotelListContainer.style.opacity = '0.5';

                // Fetch results
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    hotelListContainer.innerHTML = html;
                    hotelListContainer.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error:', error);
                    hotelListContainer.style.opacity = '1';
                });
            }
        });

        // Search Autocomplete
        const searchInput = document.getElementById('city-search-input');
        const searchResults = document.getElementById('search-results');
        let debounceTimer;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value;

                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    searchResults.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`/hotels/search?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            
                            if (data.length > 0) {
                                data.forEach(hotel => {
                                    const div = document.createElement('div');
                                    div.className = 'p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors';
                                    div.innerHTML = `
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                                <img src="${hotel.image_url || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=100&q=80'}" class="w-full h-full object-cover" alt="${hotel.name}">
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">${hotel.name}</div>
                                                <div class="text-sm text-gray-500 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    ${hotel.city}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    div.addEventListener('click', () => {
                                        window.location.href = `/hotels/${hotel.id}`;
                                    });
                                    searchResults.appendChild(div);
                                });
                            } else {
                                searchResults.innerHTML = `
                                    <div class="p-4 text-center text-gray-500">
                                        <p class="font-medium">Tidak ditemukan</p>
                                        <p class="text-xs mt-1">Coba kata kunci lain</p>
                                    </div>
                                `;
                            }
                            searchResults.classList.remove('hidden');
                        })
                        .catch(error => console.error('Error:', error));
                }, 300);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection

