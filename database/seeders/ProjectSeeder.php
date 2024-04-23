<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::truncate();

        for ($i = 1; $i <= 10; $i++) {

            $project = new Project();

            $project->fill([
                'user_id' => 1,
                'up_or_down' => rand(0, 1),
                'project_name' => 'Project ' . $i,
                'project_url' => 'http://project_management_system.test/',
                'project_logo_path' => '',
                'google_rank' => rand(1, 95),
                'time' => 2400,
                'total_update' => rand(5, 10),
                'is_backup_active' => rand(0, 1),
                'total_site_helth' => rand(1, 5),
                'total_php_issue' => rand(2, 20),
                'wp_admin_url' => 'https://wordpress.org/documentation/'
            ])->save();

            $this->storeFile($project);
        }

    }

    private function storeFile(Project $project)
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
            'project_logo_path' => '/uploads/websites/' . $project->id . '/' . $fileName,
        ])->save();
    }
}