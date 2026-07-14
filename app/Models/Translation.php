<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];
    protected $casts = ['value' => 'array'];

    public function histories(): HasMany
    {
        return $this->hasMany(TranslationHistory::class);
    }
}
