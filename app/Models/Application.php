<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'message', 'comment'];

    const RESOLVED = 0;
    const ACTIVE = 1;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
