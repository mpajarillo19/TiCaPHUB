<?php

namespace App\Http\Controllers;

use App\Jobs\RegisterUserJob;
use App\Models\Group;
use App\Models\School;
use App\Models\Specialization;
use App\Models\Ticap;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{   
    public function invitationForm() {
        $user = User::find(1);
        $ticap = Ticap::find($user->ticap_id);
        
        if($ticap->invitation_is_set) {
            return redirect()->route('users');
        }

        $title = 'User Accounts';

        $scripts = [
            asset('js/modal.js'),
            asset('js/useraccounts/addSpecialization.js'),
            // asset('js/useraccounts/schoolCheckbox.js'),
        ];

        return view('user-accounts.set-invitation', [
            'title' => $title,
            'scripts' => $scripts,
        ]);
    }

    public function setInvitation(Request $request) {
        $request->validate([
            'FEU_Diliman' => 'numeric',
            'FEU_Alabang' => 'numeric',
        ]);

        if($request->FEU_Diliman != null) {
            $school = School::find($request->FEU_Diliman);
            $school->is_involved = 1;
            $school->save();
        } 
        
        if($request->FEU_Alabang != null) {
            $school = School::find($request->FEU_Alabang);
            $school->is_involved = 1;
            $school->save();
        } 

        Auth::user()->ticap_id;
        $ticap = Ticap::find(Auth::user()->ticap_id);
        $ticap->invitation_is_set = 1;
        $ticap->save();

        return redirect()->route('users');
    }

    public function fetchSpecializations(){
        $specializations = Specialization::all();

        return response()->json([
            'specializations' =>  $specializations,
        ]);
    }

    public function addSpecialization(Request $request) {
        $validator = Validator::make($request->all(), [
            'specialization' => 'required|unique:specializations,name',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag(),
            ]);
        } else {
            Specialization::create([
                'name' => Str::title($request->specialization)
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Specialization Added SuccessFully',
            ]);
        }
    }

    public function deleteSpecialization(Request $request) {
        $validator = Validator::make($request->all(), [
            'specialization_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag(),
            ]);
        } else {
            Specialization::find($request->specialization_id)->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Specialization Deleted SuccessFully',
            ]);
        }
    }

    public function userForm() {
        $title = 'User Accounts';

        $schools = School::all();
        $roles = Role::all();
        $specializations = Specialization::all();

        $scripts = [
            asset('js/modal.js'),
        ];
        
        return view('user-accounts.add', [
            'title' => $title,
            'scripts' => $scripts,
            'schools' => $schools,
            'roles' => $roles,
            'specializations' => $specializations,
        ]);
    }

    public function adduser(Request $request) {
        $request->validate([
            'role' => 'required',
            'school' => 'required',
            'specialization' => 'required',
            'first_name' => 'required',
            'middle_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'student_number' => 'required|numeric|max:99999999999|unique:users,student_number',
            'group' => 'required',
        ]); 

        // GET PRESENT TICAP
        $ticap = Auth::user()->ticap_id;

        // GENERATE DEFAULT PASSWORD
        $tempPassword = "picab" . $request->student_number;

        // CREATE USER
        $user = User::create([
            'first_name' => Str::title($request->first_name),
            'middle_name' => Str::title($request->middle_name),
            'last_name' => Str::title($request->last_name),
            'email' => $request->email,
            'student_number' => $request->student_number,
            'ticap_id' => $ticap,
            'password' => Hash::make($tempPassword),
        ]);

        // ADD USER WITH SCHOOL AND SPECIALIZATION
        $user->userProgram()->create([
            'school_id' => $request->school,
            'specialization_id' => $request->specialization,
        ]);

        // CHECK IF GROUP ALREADY EXIST
        $groupName = Str::upper($request->group);
        if(!Group::where('name', $groupName)->exists()) {
            // CREATE GROUP
            $group = Group::create([
                'name' => $groupName,
                'specialization_id' => $request->school,
                'school_id' => $request->specialization,
            ]);

            $user->userGroup()->create([
                'group_id' => $group->id,
            ]);
        } else {
            // ADD USER TO EXISTING GROUP
            $group = Group::where('name', $groupName)->first();
            $user->userGroup()->create([
                'group_id' => $group->id,
            ]);
        };

        // SEND LINK FOR CHANGING PASSWORD TO USER
        $token = Str::random(60) . time();
        $link = URL::temporarySignedRoute('set-password', now()->addDays(7), [
            'token' => $token, 
            'ticap' => $ticap,
            'email' => $request->email,
        ]);
        $details = [
            'title' => 'Welcome to TICaP Hub ' . $request->email,
            'body' => "You are invited! Click the link below",
            'link' => $link,
        ];

        DB::table('register_users')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        dispatch(new RegisterUserJob($request->email, $details));
        
        $request->session()->flash('msg', 'Email has been sent successfully');
        $request->session()->flash('status', 'green');
        return back();
    
    }

    public function setPasswordForm(Request $request) {
        return view('user-accounts.set-password', [
            'token' => $request->token,
            'ticap' => $request->ticap,
            'email' => $request->email,
        ]);
    }

    public function setPassword(Request $request) {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        // CHECK IF EMAIL AND TOKEN EXISTS
        $user = DB::table('register_users')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->exists();

        if(!$user){
            return back()->with('error', 'Current doesn\'t match the expected account.');
        } 
        
        // DELETE REGISTER TOKEN
        DB::table('register_users')->where('token', $request->token)->delete();

        // UPDATE USER PASSWORD
        $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        

        return redirect()->route('login');
    
    }

    public function importUsers() {
        $title = 'User Accounts';

        return view('user-accounts.upload', [
            'title' => $title,
        ]);
    }

    public function resetUsers() {
        User::role('student')->delete();
        return back();
    }
}
