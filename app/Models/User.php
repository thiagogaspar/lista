<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    const ROLE_ADMIN = 'admin';

    const ROLE_EDITOR = 'editor';

    const ROLE_VIEWER = 'viewer';

    public static array $roles = [self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_VIEWER];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function hasFavorited(Model $model): bool
    {
        return $this->favorites()
            ->where('favoriteable_type', $model->getMorphClass())
            ->where('favoriteable_id', $model->id)
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR || $this->isAdmin();
    }

    public function isViewer(): bool
    {
        return true; // everyone can view
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
