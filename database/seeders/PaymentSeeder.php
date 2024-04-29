<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::truncate();

        Payment::create([
            'client_id' => 1,
            'website_id' => 1,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);

        Payment::create([
            'client_id' => 1,
            'website_id' => 2,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);

        Payment::create([
            'client_id' => 2,
            'website_id' => 3,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);

        Payment::create([
            'client_id' => 2,
            'website_id' => 3,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);

        Payment::create([
            'client_id' => 3,
            'website_id' => 10,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);


        Payment::create([
            'client_id' => 4,
            'website_id' => 8,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);

        Payment::create([
            'client_id' => 4,
            'website_id' => 9,
            'payment_date' => Carbon::now(),
            'payment_time' =>Carbon::now(),
            'amount' => 5000,
            'status' => 'Success',
            'last_try' => Carbon::now(),
            'last_success' => Carbon::now(),
        ]);
    }
}