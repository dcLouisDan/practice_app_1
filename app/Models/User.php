<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_picture'
    ];

    protected $appends = ['profile_picture_url'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Chirp::class);
    }

    public function chirps(): HasMany
    {
        return $this->hasMany(Chirp::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    protected function profilePictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['profile_picture']
                ? asset('storage/' . $attributes['profile_picture'])
                : asset('images/profile_placeholder.png')
        );
    }

    public function unfollowedUsers($perPage = 10)
    {
        $followingIds = $this->following()->pluck('users.id')->toArray();

        return User::whereNotIn('id', $followingIds)
            ->where('id', '<>', $this->id)
            ->with('followers')
            ->paginate($perPage);
    }

    public function rechirps(): HasMany
    {
        return $this->hasMany(Rechirp::class)->with('chirp');
    }
}
