<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hour',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'assings', 'course_id', 'user_id');
    }
}
