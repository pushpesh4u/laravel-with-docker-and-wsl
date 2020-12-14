<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProvidersValidation;

class Providers extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function providers_validation() {
        return $this->hasMany(ProvidersValidation::class, 'provider_id');
    }
}
