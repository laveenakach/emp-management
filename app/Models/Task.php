<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'due_date',
        'created_by',
        'assigned_to',
        'assigned_by',
        'role',
        'file_path'
    ];

    protected $casts = [
        'assigned_to' => 'array',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'users', 'id', 'id')
            ->whereIn('id', json_decode($this->assigned_to));
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
