<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'country',
        'flag',
        'language',
        'timezone',
        'game_id',
        'game_name',
        'game_image'
    ];

    // public function comments() {
    //     return $this->hasMany(Comment::class)->onDelete('cascade');
    // }
}
