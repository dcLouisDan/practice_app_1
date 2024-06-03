<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChirpMedia extends Model
{
    use HasFactory;

    protected $fillable = ['chirp_id', 'media_path', 'media_type'];

    protected $appends = ['media_url'];

    public function chirp(): BelongsTo
    {
        return $this->belongsTo(Chirp::class);
    }


    protected function mediaUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['media_path']
                ? asset('storage/' . $attributes['media_path'])
                : asset('images/media_placeholder.png')
        );
    }
}
