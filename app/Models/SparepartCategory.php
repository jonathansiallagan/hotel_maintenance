<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function spareparts()
    {
        return $this->hasMany(Sparepart::class, 'sparepart_category_id');
    }
}
