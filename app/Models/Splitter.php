<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Splitter extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Splitter $splitter): void {
            $portCount = match ($splitter->capacity) {
                '1:4' => 4,
                '1:8' => 8,
                '1:16' => 16,
                default => 0,
            };

            if ($portCount < 1) {
                return;
            }

            $splitter->ports()->createMany(
                array_map(
                    fn (int $portNumber) => [
                        'port_number' => $portNumber,
                        'status' => 'Kosong',
                    ],
                    range(1, $portCount)
                )
            );
        });
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    public function ports(): HasMany
    {
        return $this->hasMany(SplitterPort::class);
    }
}
