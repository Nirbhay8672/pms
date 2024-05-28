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
            'website_name' => 'Website 1',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
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
            'website_name' => 'Website 2',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
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


        // website 3
        $website_3 = Website::create([
            'website_name' => 'Website 3',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
            'custom_key' => $this->generateRandomKey(),
        ]);

        $this->storeFile($website_3);

        WebsiteDetails::create([
            'website_id' => $website_3->id,
            'wp_status' => 85,
            'site_views' => 3000,
            'speed_by_google' => 48,
            'accessibility_score' => 56,
            'seo_score' => 78,
            'security' => 23,
            'code_quality' => 85,
        ]);

        // website 4
        $website_4 = Website::create([
            'website_name' => 'Website 4',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
            'custom_key' => $this->generateRandomKey(),
        ]);

        $this->storeFile($website_4);

        WebsiteDetails::create([
            'website_id' => $website_4->id,
            'wp_status' => 99,
            'site_views' => 25000,
            'speed_by_google' => 97,
            'accessibility_score' => 89,
            'seo_score' => 78,
            'security' => 80,
            'code_quality' => 80,
        ]);


        // website 5
        $website_5 = Website::create([
            'website_name' => 'Website 5',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
            'custom_key' => $this->generateRandomKey(),
        ]);

        $this->storeFile($website_5);

        WebsiteDetails::create([
            'website_id' => $website_5->id,
            'wp_status' => 89,
            'site_views' => 25000,
            'speed_by_google' => 87,
            'accessibility_score' => 78,
            'seo_score' => 89,
            'security' => 56,
            'code_quality' => 54,
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