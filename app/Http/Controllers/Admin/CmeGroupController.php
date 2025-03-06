<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmeGroup;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CmeGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    /**
     * Display a listing of the CME groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $upcomingGroups = CmeGroup::where('session_date', '>=', Carbon::today())
            ->with(['staff', 'interviews.candidate'])
            ->orderBy('session_date')
            ->get();
            
        $pastGroups = CmeGroup::where('session_date', '<', Carbon::today())
            ->with(['staff', 'interviews.candidate'])
            ->orderBy('session_date', 'desc')
            ->get();
            
        return view('admin.cme.index', compact('upcomingGroups', 'pastGroups'));
    }

    /**
     * Show the form for creating a new CME group.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staffMembers = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->get();
        
        $availableCandidates = User::whereHas('role', function($query) {
            $query->where('name', 'candidate');
        })
        ->whereDoesntHave('interviews', function($query) {
            $query->where('interview_type', 'CME');
        })
        ->get();
        
        return view('admin.cme.create', compact('staffMembers', 'availableCandidates'));
    }

    /**
     * Store a newly created CME group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'session_date' => 'required|date|after_or_equal:today',
            'session_time' => 'required|in:morning,afternoon',
            'staff_id' => 'required|exists:users,id',
            'candidates' => 'required|array|min:1|max:4',
            'candidates.*' => 'exists:users,id',
            'location' => 'required|string',
        ]);

        // Create the CME group
        $group = CmeGroup::create([
            'name' => $validatedData['name'],
            'session_date' => $validatedData['session_date'],
            'session_time' => $validatedData['session_time'],
            'staff_id' => $validatedData['staff_id'],
        ]);
        
        // Create interviews for each candidate in the group
        foreach ($validatedData['candidates'] as $candidateId) {
            Interview::create([
                'candidate_id' => $candidateId,
                'staff_id' => $validatedData['staff_id'],
                'interview_date' => $validatedData['session_date'],
                'interview_time' => $validatedData['session_time'] == 'morning' ? '09:00:00' : '14:00:00',
                'interview_type' => 'CME',
                'location' => $validatedData['location'],
                'status' => 'scheduled',
                'cme_group_id' => $group->id
            ]);
        }
        
        return redirect()->route('admin.cme.index')->with('success', 'Groupe CME créé avec succès.');
    }

    /**
     * Display the specified CME group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = CmeGroup::with(['staff', 'interviews.candidate'])->findOrFail($id);
        return view('admin.cme.show', compact('group'));
    }

    /**
     * Show the form for editing the specified CME group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $group = CmeGroup::with(['interviews.candidate'])->findOrFail($id);
        
        $staffMembers = User::whereHas('role', function($query) {
            $query->where('name', 'staff');
        })->get();
        
        // Get candidates already in this group
        $groupCandidateIds = $group->interviews->pluck('candidate_id')->toArray();
        
        // Get candidates not in any CME group or already in this group
        $availableCandidates = User::whereHas('role', function($query) {
            $query->where('name', 'candidate');
        })
        ->where(function($query) use ($groupCandidateIds) {
            $query->whereDoesntHave('interviews', function($q) {
                $q->where('interview_type', 'CME');
            })
            ->orWhereIn('id', $groupCandidateIds);
        })
        ->get();
        
        return view('admin.cme.edit', compact('group', 'staffMembers', 'availableCandidates'));
    }

    /**
     * Update the specified CME group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_time' => 'required|in:morning,afternoon',
            'staff_id' => 'required|exists:users,id',
            'candidates' => 'required|array|min:1|max:4',
            'candidates.*' => 'exists:users,id',
            'location' => 'required|string',
        ]);

        $group = CmeGroup::findOrFail($id);
        
        // Update the CME group
        $group->update([
            'name' => $validatedData['name'],
            'session_date' => $validatedData['session_date'],
            'session_time' => $validatedData['session_time'],
            'staff_id' => $validatedData['staff_id'],
        ]);
        
        // Get current candidates in the group
        $currentCandidateIds = $group->interviews->pluck('candidate_id')->toArray();
        
        // Candidates to remove
        $removeCandidateIds = array_diff($currentCandidateIds, $validatedData['candidates']);
        
        // Remove interviews for candidates no longer in the group
        if (!empty($removeCandidateIds)) {
            Interview::where('cme_group_id', $group->id)
                ->whereIn('candidate_id', $removeCandidateIds)
                ->delete();
        }
        
        // Candidates to add
        $addCandidateIds = array_diff($validatedData['candidates'], $currentCandidateIds);
        
        // Add interviews for new candidates
        foreach ($addCandidateIds as $candidateId) {
            Interview::create([
                'candidate_id' => $candidateId,
                'staff_id' => $validatedData['staff_id'],
                'interview_date' => $validatedData['session_date'],
                'interview_time' => $validatedData['session_time'] == 'morning' ? '09:00:00' : '14:00:00',
                'interview_type' => 'CME',
                'location' => $validatedData['location'],
                'status' => 'scheduled',
                'cme_group_id' => $group->id
            ]);
        }
        
        // Update existing interviews
        Interview::where('cme_group_id', $group->id)
            ->whereIn('candidate_id', array_intersect($currentCandidateIds, $validatedData['candidates']))
            ->update([
                'staff_id' => $validatedData['staff_id'],
                'interview_date' => $validatedData['session_date'],
                'interview_time' => $validatedData['session_time'] == 'morning' ? '09:00:00' : '14:00:00',
                'location' => $validatedData['location'],
            ]);
        
        return redirect()->route('admin.cme.index')->with('success', 'Groupe CME mis à jour avec succès.');
    }

    
    public function destroy($id)
    {
        $group = CmeGroup::findOrFail($id);
        
        $group->delete();
        
        return redirect()->route('admin.cme.index')->with('success', 'Groupe CME supprimé avec succès.');
    }
}