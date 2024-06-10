<?php

namespace Database\Seeders;

use App\Models\Website;
use App\Models\WebsiteDetails;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        Website::truncate();
        WebsiteDetails::truncate();

        // website 1
        $website_1 = Website::create([
            'website_name' => 'WP-PMS',
            'website_url' => 'http://localhost/wp-pms/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'http://localhost/wp-pms/wp-admin/',
            'wp_username' => 'admin',
            'wp_password' => '@LHeIBWJrnr)*YY$Gp',
            'custom_key' => $this->generateRandomKey(),
        ]);

        $this->storeFile($website_1);

        WebsiteDetails::create([
            'website_id' => $website_1->id,
            'wp_status' => 10,
            'site_views' => 2312,
            'speed_by_google' => 89,
            'accessibility_score' => 48,
            'seo_score' => 59,
            'security' => 78,
            'code_quality' => 100,
        ]);
        

        // website 2
        $website_2 = Website::create([
            'website_name' => 'WP-Test',
            'website_url' => 'http://localhost/wp-test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'http://localhost/wp-test/wp-admin/',
            'wp_username' => 'nirbhay',
            'wp_password' => '@LHeIBWJrnr)*YY$Gp',
            'custom_key' => $this->generateRandomKey(),
        ]);

        $this->storeFile($website_2);

        WebsiteDetails::create([
            'website_id' => $website_2->id,
            'wp_status' => 36,
            'site_views' => 4500,
            'speed_by_google' => 69,
            'accessibility_score' => 89,
            'seo_score' => 45,
            'security' => 56,
            'code_quality' => 90,
        ]);
    }

    private function storeFile(Website $project)
    {
        $sourceFilePath = public_path('/images/wordpress.png');
        $destinationPath = public_path('/uploads/websites/' . $project->id);
        $fileName = time() . '.jpg';

        if (File::exists($destinationPath . '/' . $fileName)) {
            return 'File already exists!';
        }

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        File::copy($sourceFilePath, $destinationPath . '/' . $fileName);

        $project->fill([
            'website_logo_path' => '/uploads/websites/' . $project->id . '/' . $fileName,
        ])->save();
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