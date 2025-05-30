<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'biography',
        'birth_date',
        'death_date',
        'nationality',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    /**
     * Get the books for the author.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the age of the author (if alive) or age at death.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        $endDate = $this->death_date ?? now();
        return $this->birth_date->diffInYears($endDate);
    }

    /**
     * Check if the author is still alive.
     */
    public function isAlive(): bool
    {
        return is_null($this->death_date);
    }
}
