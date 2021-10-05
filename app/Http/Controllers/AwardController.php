<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Criteria;
use App\Models\Rubric;
use App\Models\Specialization;
use App\Models\SpecializationPanelist;
use App\Models\Ticap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AwardController extends Controller
{
    public function index() {
        $ticap = Ticap::find(Auth::user()->ticap_id);
        if(!$ticap->invitation_is_set) {
            return redirect()->route('set-invitation');
        }
        $title = 'Project Assessment';
        $scripts = [
            asset('js/awards/addAward.js'),
        ];
        return view('awards.index', [
            'title' => $title,
            'scripts' => $scripts,
        ]);
    }

    public function setRubrics() {
        $title = 'Project Assessment';
        $specializations = Specialization::all();
        $scripts = [
            asset('js/awards/set-rubric.js'),
        ];
        return view('awards.set-rubrics', [
            'title' => $title,
            'scripts' => $scripts,
            'specializations' => $specializations
        ]);
    }

    public function createRubric() {
        $title = 'Project Assessment';
        $scripts = [
            asset('js/awards/create-rubric.js'),
        ];
        return view('awards.create-rubric', [
            'title' => $title,
            'scripts' => $scripts,
        ]);
    }

    public function addRubric(Request $request) {
        $request->validate([
            'name' => 'required',
            'criteria' => 'required',
            'percentages' => 'required',
        ]);
        $rubric = Rubric::create([
            'name' => $request->name
        ]);
        for($x = 0; $x < count($request->criteria); $x++) {
            Criteria::create([
                'name' => $request->criteria[$x],
                'percentage' => $request->percentages[$x],
                'rubric_id' => $rubric->id
            ]);
        }
        session()->flash('status', 'green');
        session()->flash('message', 'Rubric successfully created');
        return redirect()->route('set-rubrics');
    }

    public function setPanelist() {
        $title = 'Project Assessment';
        $scripts = [
            asset('js/awards/set-panelist.js'),
        ];
        return view('awards.set-panelists', [
            'title' => $title,
            'scripts' => $scripts,
        ]);
    }

    public function assignPanelist() {
        $title = 'Project Assessment';
        $specs = Specialization::all();
        $panelists = User::role('panelist')->get();
        $scripts = [
            asset('js/awards/add-panelist.js'),
        ];
        return view('awards.assign-panelists', [
            'title' => $title,
            'panelists' => $panelists,
            'specs' => $specs,
            'scripts' => $scripts,
        ]);
    }

    public function assign(Request $request) {
        $request->validate([
            'specialization' => 'required',
            'panelists.*' => 'required',
        ]);

        $spec = Specialization::find($request->specialization);
        foreach($request->panelists as $p) {
            if(SpecializationPanelist::where('user_id', $p)->exists()) {
                $user = User::find($p);
                session()->flash('status', 'red');
                session()->flash('message', $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' already assigned');
                return back();
            }
            $spec->panelists()->create([
                'user_id' => $p
            ]);
        }
        session()->flash('status', 'green');
        session()->flash('message', 'Panelist successfully set');
        return redirect('/set-panelist');
    }

    public function fetchPanelists() {
        $panelists = User::role('panelist')->get();
        return response()->json([
            'status' => 200,
            'panelists' => $panelists
        ]);
    }
}
