<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    /** @use HasFactory<\Database\Factories\CreatorFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the creator profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
