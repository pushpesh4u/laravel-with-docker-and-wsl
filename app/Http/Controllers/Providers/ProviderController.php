<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Providers;

class ProviderController extends Controller
{
    private $translators = [
        'aspect_ratio' => 'Aspect ratio should be equal to ',
        'size' => 'Size should be ',
        'length' => 'Length should be ',
        '<' => 'less than ',
        '>' => 'greater than '
    ];

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
                $description = $this->translateToEnglish($validation);

                $response[$provider]['description'][$description['image_type']]['restrictions'][] = $description['restrictions'];
                $response[$provider]['description'][$description['image_type']]['notes'] = $description['notes'];
            }
        }

        return response($response, $responseCode)
                ->header('Content-Type', 'application/json');
    }

    private function translateToEnglish($validation) {
        $condition = $validation->image_type;
        $restriction_params = $validation->restriction_params;
        $restriction_values = $validation->restriction_values;
        $notes = $validation->notes;

        $return = [];

        switch($condition) {
            case '.jpg':
            case '.mp4':
            case '.mp3':
            case '.gif':
            case '.mov':
                $return = [
                    'image_type' => $condition,
                    'restrictions' => $this->checkConditions($restriction_params, $restriction_values),
                    'notes' => $notes
                ];
                break;
            default:
                break;
        }

        return $return;
    }

    private function checkConditions($restriction_params, $restriction_values) {
        $result = '';
        switch($restriction_params) {
            case 'aspect_ratio':
                $result = $this->translators[$restriction_params] . $restriction_values;
                break;
            case 'size':
                $restriction_values = explode(' ', $restriction_values);
                $check = $this->translators[array_shift($restriction_values)];
                $restriction_values = implode(' ', $restriction_values);

                $result = $this->translators[$restriction_params] . $check . $restriction_values;
                break;
            case 'length':
                $restriction_values = explode(' ', $restriction_values);
                $check = $this->translators[array_shift($restriction_values)];
                $restriction_values = implode(' ', $restriction_values);

                $result = $this->translators[$restriction_params] . $check . $restriction_values;
                break;
            default:
                break;
        }

        return $result;
    }

    public function __destruct() {
        
    }
}
