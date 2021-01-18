<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function items(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function parent_rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class, 'parent_id');
    }

    public function children_rubric(): HasMany
    {
        return $this->hasMany(Rubric::class, 'parent_id');
    }
}
