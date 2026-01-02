<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Room extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'price_per_night',
        'quantity',
        'image',
        'images',
        'is_active',
        'description',
    ];

    protected $casts = [
        'images' => 'array',
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    // Fix #7: Add image file validation with fallback
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        $path = storage_path('app/public/' . $this->image);
        if (!file_exists($path)) {
            Log::warning("Missing room image file: {$this->image}", ['room_id' => $this->id]);
            return asset('images/default-room.jpg'); // Fallback to default image
        }
        
        return asset('storage/' . $this->image);
    }
}
