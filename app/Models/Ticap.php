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
        'election_review',
        'election_finished'
    ];

    public function candidates() {
        return $this->belongsToMany(Candidate::class, 'elections', 'ticap_id', 'candidate_id');
    }
    public function userGroups() {
        return $this->hasMany(UserProgram::class, 'ticap_id', 'id');
    }
    public function groups() {
        return $this->hasMany(Group::class, 'ticap_id', 'id');
    }
    public function groupExhibits() {
        return $this->hasMany(GroupExhibit::class, 'ticap_id', 'id');
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
    public function archivedOfficers() {
        return $this->hasOne(TicapOfficer::class, 'ticap_id', 'id');
    }
    public function archivedGroups() {
        return $this->hasOne(TicapGroup::class, 'ticap_id', 'id');
    }
    public function archivedExhibits() {
        return $this->hasMany(TicapExhibit::class, 'ticap_id', 'id');
    }
    public function archivedCommittees() {
        return $this->hasOne(TicapCommittee::class, 'ticap_id', 'id');
    }
}
