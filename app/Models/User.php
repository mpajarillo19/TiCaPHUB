<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'ticap_id',
        'email_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function userElection() {
        return $this->hasOne(UserElection::class, 'user_id', 'id');
    }
    public function school() {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
    public function userSpecialization() {
        return $this->hasOne(UserSpecialization::class, 'user_id', 'id');
    }
    public function candidate() {
        return $this->hasOne(Candidate::class, 'user_id', 'id');
    }
    public function officer() {
        return $this->hasOne(Officer::class, 'user_id', 'id');
    }
    public function votes() {
        return $this->hasMany(Vote::class, 'user_id', 'id');
    }
    public function userGroup() {
        return $this->hasOne(UserGroup::class, 'user_id', 'id');
    }
    public function lists() {
        return $this->hasMany(TaskList::class, 'user_id', 'id');
    }
    public function tasks() {
        return $this->belongsToMany(Task::class, 'user_task', 'user_id', 'task_id')
        ->withPivot('is_read')
        ->withTimestamps();
    }
    public function tasksCreated() {
        return $this->hasMany(Task::class, 'user_id', 'id');
    }
    public function activities() {
        return $this->hasMany(Activity::class, 'user_id', 'id');
    }
    public function scopeSearch($query, $term) {
        $term  = "%$term%";
        $query->where(function($query) use ($term){
            $query->where('first_name', 'LIKE', $term)
                ->orWhere('middle_name', 'LIKE', $term)
                ->orWhere('last_name', 'LIKE', $term);
        });
    }
    public function committee() {
        return $this->hasOne(Committee::class, 'user_id', 'id');
    }
    public function committeeMember() {
        return $this->hasOne(CommitteeMember::class, 'user_id', 'id');
    }
    public function committeeTasks() {
        return $this->belongsToMany(CommitteeTask::class, 'member_task', 'user_id', 'task_id')
            ->withPivot('is_read')
            ->withTimestamps();
    }
    public function specializationPanelist() {
        return $this->hasOne(SpecializationPanelist::class, 'user_id', 'id');
    }
}
