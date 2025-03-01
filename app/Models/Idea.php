<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'likes',
    ];

    protected $casts = ['create_at' => 'datetime'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeMyIdea(Builder $query, $filter)
    {
        if(!empty($filter) && $filter == 'mis-ideas') {
            return $query->where('user_id', auth()->id());
        }
    }

    public function scopeTheBest(Builder $query, $filter)
    {
        if(!empty($filter) && $filter == 'las-mejores') {
            return $query->orderBy('likes', 'desc');
        }
    }
}
