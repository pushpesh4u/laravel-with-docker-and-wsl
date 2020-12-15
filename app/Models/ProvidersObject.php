<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Providers;

class ProvidersObject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'providers_object';

    public function providers()
    {
        return $this->belongsTo(Providers::class);
    }
}
