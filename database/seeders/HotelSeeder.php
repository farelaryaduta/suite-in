<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Grand Jakarta Hotel',
                'description' => 'Luxurious 5-star hotel in the heart of Jakarta with world-class amenities and exceptional service.',
                'address' => 'Jl. Thamrin No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10230',
                'phone' => '+62-21-12345678',
                'email' => 'info@grandjakarta.com',
                'star_rating' => 5,
                'rating' => 4.8,
                'total_reviews' => 1250,
            ],
            [
                'name' => 'Bali Beach Resort',
                'description' => 'Beautiful beachfront resort with stunning ocean views and tropical paradise atmosphere.',
                'address' => 'Jl. Pantai Kuta',
                'city' => 'Bali',
                'province' => 'Bali',
                'postal_code' => '80361',
                'phone' => '+62-361-987654',
                'email' => 'info@balibeach.com',
                'star_rating' => 4,
                'rating' => 4.6,
                'total_reviews' => 890,
            ],
            [
                'name' => 'Bandung Mountain View',
                'description' => 'Scenic hotel overlooking the mountains with cool climate and fresh air.',
                'address' => 'Jl. Dago No. 45',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40135',
                'phone' => '+62-22-7654321',
                'email' => 'info@bandungmountain.com',
                'star_rating' => 4,
                'rating' => 4.5,
                'total_reviews' => 650,
            ],
            [
                'name' => 'Yogyakarta Heritage Hotel',
                'description' => 'Charming hotel combining traditional Javanese architecture with modern comfort.',
                'address' => 'Jl. Malioboro No. 123',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'postal_code' => '55271',
                'phone' => '+62-274-5551234',
                'email' => 'info@jogjaheritage.com',
                'star_rating' => 3,
                'rating' => 4.3,
                'total_reviews' => 420,
            ],
            [
                'name' => 'Surabaya Business Hotel',
                'description' => 'Modern business hotel with excellent facilities for corporate travelers.',
                'address' => 'Jl. Pemuda No. 78',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60271',
                'phone' => '+62-31-8889999',
                'email' => 'info@surabayabusiness.com',
                'star_rating' => 4,
                'rating' => 4.4,
                'total_reviews' => 580,
            ],
        ];

        $roomTypes = RoomType::all();
        $amenities = Amenity::all();

        // Get admin user to assign as owner
        $admin = \App\Models\User::where('role', 'admin')->first();
        
        foreach ($hotels as $hotelData) {
            // Ensure status is set to active
            $hotelData['status'] = 'active';
            $hotelData['owner_id'] = $admin->id ?? null;
            $hotel = Hotel::create($hotelData);

            // Attach hotel amenities
            $hotelAmenities = $amenities->where('type', 'hotel')->merge($amenities->where('type', 'both'));
            $hotel->amenities()->attach($hotelAmenities->pluck('id'));

            // Create rooms for each hotel
            foreach ($roomTypes as $roomType) {
                $priceBase = rand(300000, 800000);
                
                // Get first letter of room type, but make it unique by adding room_type_id
                $roomPrefix = strtoupper(substr($roomType->name, 0, 1)) . $roomType->id;
                
                for ($i = 1; $i <= 3; $i++) {
                    $room = Room::create([
                        'hotel_id' => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'room_number' => $roomPrefix . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'price_per_night' => $priceBase + ($roomType->id * 100000),
                        'quantity' => 1,
                        'description' => $roomType->description,
                    ]);

                    // Attach room amenities
                    $roomAmenities = $amenities->where('type', 'room')->merge($amenities->where('type', 'both'));
                    $room->amenities()->attach($roomAmenities->pluck('id'));
                }
            }
        }
    }
}
