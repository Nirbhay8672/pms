<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        Member::truncate();

        // member 1
        Member::create([
            'username' => 'Nirbhay',
            'email' => 'hathaliyank@gmail.com',
            'phone_number' => '8200186326',
            'website_name' => 'PMS',
            'website_link' => 'http://localhost/wp-pms/',
            'wp_username' => 'Nux',
            'wp_password' => '@LHeIBWJrnr)*YY$Gp',
            'otech_username' => 'admin',
            'otech_password' => 'admin@123',
            'licence_key' => $this->generateRandomKey(),
            'plugin_version' => 1.2,
            'send_update' => 1,
        ]);

        // member 2
        Member::create([
            'username' => 'Devendra',
            'email' => 'devendra@gmail.com',
            'phone_number' => '9954585485',
            'website_name' => 'TEST',
            'website_link' => 'http://localhost/wp-test/',
            'wp_username' => 'Dev',
            'wp_password' => '@LHeIBWJrnr)*YY$Gp',
            'otech_username' => 'admin',
            'otech_password' => 'admin@123',
            'licence_key' => $this->generateRandomKey(),
            'plugin_version' => 0.1,
            'send_update' => 0,
        ]);
    }

    public function generateRandomKey($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';    
        $charLength = strlen($characters);
        $key = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charLength - 1);    
            $key .= $characters[$randomIndex];
        }
        return $key;
    }
}