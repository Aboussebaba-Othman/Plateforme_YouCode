<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffAccountCreated;

class StaffManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function index()
    {
        $staff = User::role(['staff', 'admin'])->paginate(10);
        return view('admin.staff.index', compact('staff'));
    }
    
    public function create()
    {
        $roles = Role::whereIn('name', ['staff', 'admin'])->get();
        return view('admin.staff.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'send_email' => 'boolean'
        ]);
        
        $password = Str::random(10);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($password);
        $user->email_verified_at = now(); 
        $user->role_id = $request->role_id;
        $user->save();
        
        if ($request->has('send_email')) {
            Mail::to($user->email)
                ->send(new StaffAccountCreated($user, $password));
        }
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account created successfully')
            ->with('password', $password); 
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('name', ['staff', 'admin'])->get();
        
        $isLastAdmin = $user->hasRole('admin') && User::role('admin')->count() <= 1;
        
        return view('admin.staff.edit', compact('user', 'roles', 'isLastAdmin'));
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1 && $request->role_id != $user->role_id) {
            return redirect()->back()
                ->with('error', 'Cannot change role of the last admin');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'reset_password' => 'boolean'
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        
        $password = null;
        if ($request->has('reset_password')) {
            $password = Str::random(10);
            $user->password = Hash::make($password);
            
            if ($request->has('send_email')) {
                Mail::to($user->email)
                    ->send(new StaffAccountCreated($user, $password, true));
            }
        }
        
        $user->save();
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account updated successfully')
            ->with('password', $password);
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Cannot delete the last admin');
        }
        
        if ($user->hasRole('staff') && $user->testSessions()->where('date', '>=', now())->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete staff with upcoming test sessions');
        }
        
        $user->delete();
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff account deleted successfully');
    }
    
    public function availability($id)
    {
        $user = User::role('staff')->findOrFail($id);
        $availability = $user->availability ?? [];
        
        return view('admin.staff.availability', compact('user', 'availability'));
    }
    
    public function updateAvailability(Request $request, $id)
    {
        $user = User::role('staff')->findOrFail($id);
        
        $request->validate([
            'availability' => 'required|array',
            'availability.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time'
        ]);
        
        $user->availability = $request->availability;
        $user->save();
        
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff availability updated successfully');
    }
}