<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateInfoRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->isCandidate();
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'id_card' => 'required|file|mimes:jpeg,png,pdf|max:5120', 
        ];
    }
}