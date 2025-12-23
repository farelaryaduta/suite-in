    <!-- Section Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Rekomendasi Hotel Pilihan</h2>
            <p class="text-gray-500 mt-1 text-base">Penginapan nyaman dengan harga terbaik untukmu</p>
        </div>
        @if(request('city'))
            <div class="flex items-center gap-3">
                <span class="bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-sm font-bold flex items-center shadow-sm">
                    <span class="mr-2">📍</span> {{ request('city') }}
                </span>
                <a href="{{ route('home') }}" class="reset-filter-btn flex items-center text-gray-500 hover:text-red-600 transition-colors text-sm font-medium bg-white px-3 py-1.5 rounded-full border border-gray-200 hover:border-red-200 shadow-sm group" title="Hapus Filter">
                    <svg class="w-4 h-4 mr-1.5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reset
                </a>
            </div>
        @endif
    </div>
    
    @if($hotels->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($hotels as $hotel)
                <div class="group bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Image Container -->
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        @if($hotel->image)
                            <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Star Rating Badge -->
                        <div class="absolute top-3 left-3">
                            <div class="bg-white/90 backdrop-blur-md px-2 py-1 rounded-lg shadow-sm flex items-center gap-1">
                                <span class="text-yellow-500 text-xs">★</span>
                                <span class="text-gray-700 text-[10px] font-bold">{{ $hotel->star_rating }}</span>
                            </div>
                        </div>

                        <!-- Review Badge -->
                        @if($hotel->total_reviews > 0)
                        <div class="absolute top-3 right-3">
                            <div class="bg-blue-600 text-white px-2 py-1 rounded-lg shadow-md flex items-center space-x-1">
                                <span class="text-xs font-bold">{{ number_format($hotel->rating, 1) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $hotel->name }}</h3>
                        
                        <div class="flex items-center text-gray-500 text-xs mb-3">
                            <svg class="w-4 h-4 mr-1 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <span class="truncate">{{ $hotel->city }}</span>
                        </div>

                        <!-- Amenities Preview -->
                        @if($hotel->amenities->count() > 0)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach($hotel->amenities->take(2) as $amenity)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-medium">
                                    {{ $amenity->name }}
                                </span>
                            @endforeach
                            @if($hotel->amenities->count() > 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-[10px] font-medium">
                                    +{{ $hotel->amenities->count() - 2 }}
                                </span>
                            @endif
                        </div>
                        @endif

                        <!-- Price & CTA -->
                        <div class="flex items-end justify-between pt-3 border-t border-gray-100">
                            <div>
                                <p class="text-[10px] text-gray-500 mb-0.5">Mulai dari</p>
                                <div class="flex items-baseline gap-0.5">
                                    <p class="text-lg font-bold text-orange-600">Rp {{ number_format($hotel->rooms->where('is_active', true)->min('price_per_night') ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('hotels.show', $hotel->id) }}?check_in={{ request('check_in', date('Y-m-d')) }}&check_out={{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                Pilih
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> 


        <!-- Pagination -->
        <div class="mt-10">
            {{ $hotels->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Hotel tidak ditemukan</h3>
            <p class="text-gray-500 mb-6">Coba ubah kriteria pencarian atau cari di kota lain</p>
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 bg-blue-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Reset Pencarian</span>
            </a>
        </div>
    @endif