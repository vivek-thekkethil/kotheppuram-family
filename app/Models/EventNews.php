<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'item_date',
        'photo_path',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'item_date' => 'date',
        ];
    }
}
