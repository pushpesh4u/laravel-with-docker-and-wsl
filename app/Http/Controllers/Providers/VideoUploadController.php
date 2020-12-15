<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\ProvidersValidation;
use App\Models\ProvidersObject;

class VideoUploadController extends Controller
{
    public function uploadVideos(Request $request) {
        $response = [];
        $responseCode = 200;

        $validation = Validator::make($request->all(),[
            'provider' => 'required|exists:providers|int',
            'video_file' => 'required|image|mimes:mp4,mov,mp3',
            'name' => 'required|string'
        ]);

        // see if basic validation fails
        if($validation->fails()){
            $errors = $validation->errors();
            return response($errors,422);
        } else {
            // check more
            $file = $request->file('video_file');
            $extension = '.' . strtolower( $file->getClientOriginalExtension() );
            $providerID = $request->provider;

            $filter = ProvidersValidation::select('image_type', 'restriction_params', 'restriction_values', 'notes')
                                        ->where('provider_id', $providerID)
                                        ->where('image_type', $extension)
                                        ->get();

            $restrictions = [];
            foreach($filter as $check) {
                $description = translateToEnglish($check);

                $restrictions['restrictions'][$check->restriction_params] = $description['restrictions'];
                $restrictions['notes'] = $description['notes'];
            }

            $errors = [];
            $filter = $filter->toArray();

            $file_path = $file->getPathName();

            if( !empty($filter) ) {
                // need to check the restrictions
                foreach($filter as $check) {
                    switch($check['restriction_params']) {
                        case 'aspect_ratio':
                            $checkAspectRatio = checkAspectRatio($check['restriction_values'], $file_path);
                            if(!$checkAspectRatio) {
                                $errors[$check['restriction_params']] = 'Please provide an image with a correct aspect ratio. ' . $restrictions['restrictions'][$check['restriction_params']];
                            }
                            break;
                        case 'size':
                            $checkFileSize = checkFileSize($check['restriction_values'], $file->getSize());
                            if(!$checkFileSize) {
                                $errors[$check['restriction_params']] = 'Please provide an image with a correct size. ' . $restrictions['restrictions'][$check['restriction_params']];
                            }
                            break;
                        case 'length':
                            $checkContentLength = checkContentLength($check['restriction_values'], $file_path);
                            if(!$checkContentLength) {
                                $errors[$check['restriction_params']] = 'Please provide an image with a correct content length. ' . $restrictions['restrictions'][$check['restriction_params']];
                            }   
                            break;
                        default:
                            break;
                    }
                }
            } else {
                // no rules were found
                $errors['general'] = 'Request does not seem to be valid.';
            }

            $save_file_path = '';
            if( !empty($errors) ) {
                return response($errors,422);
            } else {
                // check if we need to extract any image from the video
                foreach($filter as $check) {
                    switch($check['notes']) {
                        case 'Extract preview image from middle of the video':
                            $save_file_path = storage_path('app/public/' . trim( $request->post('name') ) . '_screenshot.jpg');
                            getScreenshot($file_path, $save_file_path);
                            $save_file_path = asset('storage/' . trim( $request->post('name') ) . '_screenshot.jpg');
                            break;
                        default:
                            break;
                    }
                }
            }
            $imageSavePath = trim( $request->post('name') ) . $extension;
            $obj_file_path = storage_path('app/public/');
            $file->move($obj_file_path, $imageSavePath);
            $obj_save_path = asset('storage/' . $imageSavePath);

            $saveObj = new ProvidersObject;
            $saveObj->provider_id = $providerID;
            $saveObj->object_path = $obj_save_path;
            $saveObj->screenshot_path = $save_file_path;

            $saveObj->save();

            $response['status'] = 'Successfully stored values';
        }

        return response($response, $responseCode)
        ->header('Content-Type', 'application/json');
    }
}
