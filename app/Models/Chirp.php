<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chirp extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'parent_id',
        'quote_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Chirp::class, 'parent_id')->with('user', 'likes', 'replies', 'media', 'rechirps');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ChirpMedia::class);
    }

    public function parent()
    {
        return $this->belongsTo(Chirp::class, 'parent_id')->with('user', 'likes', 'replies', 'media', 'rechirps');
    }

    public function quotes()
    {
        return $this->hasMany(Chirp::class, 'quote_id')->with('user', 'likes', 'replies', 'media', 'rechirps');
    }

    public function quotedChirp()
    {
        return $this->belongsTo(Chirp::class, 'quote_id')->with('user', 'likes', 'replies', 'media', 'rechirps');
    }
    public function rechirps(): HasMany
    {
        return $this->hasMany(Rechirp::class)->with('user', 'chirp');
    }
}
