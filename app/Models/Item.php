<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function manufacturers(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class,'manufacturer_id', 'id');
    }

    public function rubrics(): belongsTo
    {
        return $this->belongsTo(Rubric::class,'rubric_id', 'id');
    }

    public function categories(): belongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
