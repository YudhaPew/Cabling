<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Node extends Model
{
    protected $guarded = [];

    public function splitters(): HasMany
    {
        return $this->hasMany(Splitter::class);
    }
}
