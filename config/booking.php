<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax Rate
    |--------------------------------------------------------------------------
    |
    | The tax rate charged on bookings. This amount goes to the platform (admin).
    | Default: 0.10 (10%)
    |
    */
    'tax_rate' => env('BOOKING_TAX_RATE', 0.10),

    /*
    |--------------------------------------------------------------------------
    | Service Charge Rate
    |--------------------------------------------------------------------------
    |
    | The service charge rate charged on bookings. This amount goes to the hotel partner.
    | Default: 0.05 (5%)
    |
    */
    'service_charge_rate' => env('BOOKING_SERVICE_CHARGE_RATE', 0.05),

    /*
    |--------------------------------------------------------------------------
    | Minimum Booking Days
    |--------------------------------------------------------------------------
    |
    | The minimum number of days for a booking.
    |
    */
    'min_booking_days' => env('BOOKING_MIN_DAYS', 1),

    /*
    |--------------------------------------------------------------------------
    | Maximum Booking Days
    |--------------------------------------------------------------------------
    |
    | The maximum number of days allowed for a single booking.
    |
    */
    'max_booking_days' => env('BOOKING_MAX_DAYS', 30),
];
