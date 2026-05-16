<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SplicingConnection extends Model
{
    // Mengizinkan semua kolom diisi secara massal dari form controller
    protected $guarded = [];
}
