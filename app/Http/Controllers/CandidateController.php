<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateInfoRequest;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:candidate']);
    }

    public function showProfile()
    {
        $candidate = Auth::user()->candidate;
        return view('candidate.profile', compact('candidate'));
    }

    public function showDocumentForm()
{
    $candidate = Auth::user()->candidate; 
    return view('candidate.documents', compact('candidate'));
}

    public function submitDocuments(CandidateInfoRequest $request)
    {
        $user = Auth::user();
        
        if (!$user->candidate) {
            $candidate = new Candidate(['user_id' => $user->id]);
            $candidate->save();
        } else {
            $candidate = $user->candidate;
        }
        
        $candidate->fill([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
       
        if ($request->hasFile('id_card')) {
            $path = $request->file('id_card')->store('id_cards', 'public');
            $candidate->id_card_path = $path;
        }
        
        $candidate->status = 'documents_submitted';
        $candidate->save();
        
        return redirect()->route('candidate.profile')
            ->with('success', 'Your documents have been submitted successfully.');
    }
}