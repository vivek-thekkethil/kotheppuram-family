<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'email',
        'phone',
        'photo_path',
        'date_of_birth',
        'wedding_anniversary',
        'related_member_id',
        'relationship_to_other',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'wedding_anniversary' => 'date',
        ];
    }

    public function relatedMember(): BelongsTo
    {
        return $this->belongsTo(self::class, 'related_member_id');
    }

    public function relatedMembers(): HasMany
    {
        return $this->hasMany(self::class, 'related_member_id');
    }
}
