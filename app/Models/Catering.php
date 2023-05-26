<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catering extends Model
{
    use HasFactory;
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function refLawuks()
    {
        return $this->hasMany(RefLawuk::class, 'id', 'lauk_id');
    }
    public function refSayurs()
    {
        return $this->hasMany(RefSayur::class, 'id', 'sayur_id');
    }
}
