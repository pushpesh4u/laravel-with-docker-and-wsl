<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Providers;
use App\Models\ProvidersObject;

class ProviderController extends Controller
{
    public function __construct() {
        //parent::__construct();
    }

    public function getProviders() {
        $response = [];
        $responseCode = 200;

        $data = Providers::where('status', 1)
                                ->with('providers_validation')
                                ->get();

        foreach($data as $node) {
            $provider = $node->name;
            $response[$provider] = [];

            foreach($node->providers_validation as $validation) {
                $description = translateToEnglish($validation);

                $response[$provider]['description'][$description['image_type']]['restrictions'][$validation->restriction_params] = $description['restrictions'];
                $response[$provider]['description'][$description['image_type']]['notes'] = $description['notes'];
            }
        }

        return response($response, $responseCode)
                ->header('Content-Type', 'application/json');
    }

    public function getObjects() {
        $response = [];
        $responseCode = 200;

        $response = ProvidersObject::orderBy('updated_at','desc')->paginate(10);

        return response($response, $responseCode)
        ->header('Content-Type', 'application/json');
    }

    public function __destruct() {
        
    }
}
