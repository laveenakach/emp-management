<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'description',
        'priority',
        'progress',
        'status',
        'start_date',
        'submission_file',
        'submitted_by',
        'submitted_at',
        'due_date',
        'created_by',
        'assigned_by',
        'role',
        'file_path'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    const STATUS_NOT_STARTED = 'Not Started';
    const STATUS_SUBMITTED   = 'Submitted';
    const STATUS_APPROVED    = 'Approved';
    const STATUS_REJECTED    = 'Rejected';

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

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['start_date', 'submitted_at', 'worked_minutes']);
    }

}
