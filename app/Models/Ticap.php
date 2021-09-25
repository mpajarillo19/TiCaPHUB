<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticap extends Model
{
    use HasFactory;

    protected $table = 'ticaps';
    protected $fillable = [
        'name',
        'invitation_is_set',
        'election_has_started',
        'election_finished'
    ];

    public function candidates() {
        return $this->belongsToMany(Candidate::class, 'elections', 'ticap_id', 'candidate_id');
    }
    public function userGroups() {
        return $this->hasMany(UserProgram::class, 'ticap_id', 'id');
    }
    public function awards(){
        return $this->hasMany(Award::class, 'ticap_id', 'id');
    }
    public function events() {
        return $this->hasMany(Event::class, 'ticap_id', 'id');
    }
    public function archivedEvents() {
        return $this->hasMany(TicapEvent::class, 'ticap_id', 'id');
    }
}
