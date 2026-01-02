<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Hotel extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'latitude',
        'longitude',
        'star_rating',
        'image',
        'images',
        'rating',
        'total_reviews',
        'status',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenities');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Fix #7: Add image file validation with fallback
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        $path = storage_path('app/public/' . $this->image);
        if (!file_exists($path)) {
            Log::warning("Missing hotel image file: {$this->image}", ['hotel_id' => $this->id]);
            return asset('images/default-hotel.jpg'); // Fallback to default image
        }
        
        return asset('storage/' . $this->image);
    }
}
