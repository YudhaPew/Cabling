<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    use HasFactory;

    // Pastikan kolom ini diijinkan untuk mass assignment jika belum ada
    protected $fillable = [
        'name',
        'source_node_id',
        'destination_node_id',
        'total_tube',
        'cores_per_tube',
        'description'
    ];

    /**
     * Relasi ke Node Asal (Hulu)
     */
    public function source_node()
    {
        // Menghubungkan source_node_id ke id milik tabel nodes
        return $this->belongsTo(Node::class, 'source_node_id');
    }

    /**
     * Relasi ke Node Tujuan (Hilir)
     */
    public function destination_node()
    {
        // Menghubungkan destination_node_id ke id milik tabel nodes
        return $this->belongsTo(Node::class, 'destination_node_id');
    }
}
