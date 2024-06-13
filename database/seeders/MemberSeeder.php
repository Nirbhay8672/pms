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
            'website_link' => 'https://wp-pms.000webhostapp.com/',
            'wp_username' => 'admin',
            'wp_password' => '@LHeIBWJrnr)*YY$Gp',
            'otech_username' => 'admin',
            'otech_password' => 'admin@123',
            'licence_key' => '6YEz4XE05pfeMI6lYxVzMwFpHI69aL6x',
            'plugin_version' => 1.2,
            'plugin_is_active' => 1,
            'send_update' => 1,
        ]);

        // member 2
        Member::create([
            'username' => 'Devendra',
            'email' => 'devendra@gmail.com',
            'phone_number' => '9954585485',
            'website_name' => 'TEST',
            'website_link' => 'http://localhost/wp-test/',
            'wp_username' => null,
            'wp_password' => null,
            'otech_username' => 'admin',
            'otech_password' => 'admin@123',
            'licence_key' => 'SZDPNGOPVK50r8tN7Lgwv8KXZnAC5FdL',
            'send_update' => 0,
        ]);

        // member 3
        Member::create([
            'username' => 'New',
            'email' => 'new@gmail.com',
            'phone_number' => '8200186326',
            'website_name' => 'Otech',
            'website_link' => 'otech.com/',
            'wp_username' => null,
            'wp_password' => null,
            'otech_username' => 'Admin',
            'otech_password' => 'admin@123',
            'licence_key' => 'MwOweNVc2nXzJkA9VDNcoU4JhDelMUdm',
            'send_update' => 0,
        ]);
    }
}