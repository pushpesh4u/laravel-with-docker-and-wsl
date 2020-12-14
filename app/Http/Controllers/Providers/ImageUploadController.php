<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ProvidersValidation;

class ImageUploadController extends Controller
{
    public function uploadImages(Request $request) {
        $response = ['test'];
        $responseCode = 200;

        $validation = Validator::make($request->all(),[
            'id' => 'required|exists:providers|int',
            'image' => 'required|image|mimes:jpg,gif',
            'name' => 'required|string'
        ]);

        // see if basic validation fails
        if($validation->fails()){
            $errors = $validation->errors();
            return response($errors,422);
        } else {
            // check more
            $extension = '.' . $request->image->extension();
            $providerID = $request->id;

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

            $file = $request->file('image');
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
                            $checkFileSize = checkFileSize($check['restriction_values'], $file_path);
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
            }

            if( !empty($errors) ) {
                return response($errors,422);
            }
        }

        return response($response, $responseCode)
        ->header('Content-Type', 'application/json');
    }
}
