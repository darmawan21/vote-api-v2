<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['score'];

    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'products_id', 'id'); 
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
    
    public function ref_sayur()
    {
        return $this->belongsTo(RefSayur::class, 'ref_sayur_id', 'id');
    }

    public function ref_lawuk()
    {
        return $this->belongsTo(RefLawuk::class, 'ref_lawak_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class); 
    }

    public function getScoreAttribute()
    {
        return $this->votes()->sum('score');
    }

}
