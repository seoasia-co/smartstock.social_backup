<?php

namespace App\Models\Team;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $connection = 'main_db';
    protected $table = 'team_members';
    
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'email',
        'status',
        'allow_unlimited_credits',
        'remaining_images',
        'remaining_words',
        'used_image_credit',
        'used_word_credit',
        'joined_at',
        'team_plan_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
