<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SplitterPort extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function splitter(): BelongsTo
    {
        return $this->belongsTo(Splitter::class);
    }
}
