<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProviderValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providersValidations = [
            [
                'provider_id' => 1,
                'image_type' => '.jpg',
                'restriction_params' => 'aspect_ratio',
                'restriction_values' => '4:3',
                'notes' => ''
            ],[
                'provider_id' => 1,
                'image_type' => '.jpg',
                'restriction_params' => 'size',
                'restriction_values' => '< 2 MB',
                'notes' => ''
            ],[
                'provider_id' => 1,
                'image_type' => '.mp4',
                'restriction_params' => 'length',
                'restriction_values' => '< 1 minutes',
                'notes' => ''
            ],[
                'provider_id' => 1,
                'image_type' => '.mp3',
                'restriction_params' => 'length',
                'restriction_values' => '< 30 seconds',
                'notes' => ''
            ],[
                'provider_id' => 1,
                'image_type' => '.mp3',
                'restriction_params' => 'size',
                'restriction_values' => '< 5 MB',
                'notes' => ''
            ],[
                'provider_id' => 2,
                'image_type' => '.jpg',
                'restriction_params' => 'aspect_ratio',
                'restriction_values' => '16:9',
                'notes' => ''
            ],[
                'provider_id' => 2,
                'image_type' => '.gif',
                'restriction_params' => 'aspect_ratio',
                'restriction_values' => '16:9',
                'notes' => ''
            ],[
                'provider_id' => 2,
                'image_type' => '.jpg',
                'restriction_params' => 'size',
                'restriction_values' => '< 5 MB',
                'notes' => ''
            ],[
                'provider_id' => 2,
                'image_type' => '.gif',
                'restriction_params' => 'size',
                'restriction_values' => '< 5 MB',
                'notes' => ''
            ],[
                'provider_id' => 2,
                'image_type' => '.mp4',
                'restriction_params' => 'size',
                'restriction_values' => '< 50 MB',
                'notes' => 'Extract preview image from middle of the video'
            ],[
                'provider_id' => 2,
                'image_type' => '.mov',
                'restriction_params' => 'size',
                'restriction_values' => '< 50 MB',
                'notes' => 'Extract preview image from middle of the video'
            ],[
                'provider_id' => 2,
                'image_type' => '.mp4',
                'restriction_params' => 'length',
                'restriction_values' => '< 5 minutes',
                'notes' => 'Extract preview image from middle of the video'
            ],[
                'provider_id' => 2,
                'image_type' => '.mov',
                'restriction_params' => 'length',
                'restriction_values' => '< 5 minutes',
                'notes' => 'Extract preview image from middle of the video'
            ]
        ];
        \DB::table('providers_validation')->insert($providersValidations);
    }
}
