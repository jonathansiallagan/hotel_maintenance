<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
