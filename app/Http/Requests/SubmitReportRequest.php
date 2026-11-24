<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_token'  => 'nullable|uuid',

            // All categories the form actually sends
            'category'        => 'required|in:corruption,child_abuse,bribery,harassment,violence,drug_trafficking,extortion,land_grabbing,police_misconduct,fraud,theft,other',

            // Title is optional — form doesn't have a title field
            'title'           => 'nullable|string|max:200',

            // Lowered minimum to match form's 20 char enforcement
            'description'     => 'required|string|min:20|max:5000',

            'district'        => 'required|string|max:100',
            'upazila'         => 'nullable|string|max:100',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',

            // Accept both 'files' and 'evidence' field names
            'files'           => 'nullable|array|max:5',
            'files.*'         => 'file|max:51200|mimes:jpg,jpeg,png,mp4,mov,mp3,aac,pdf,doc,docx',
            'evidence'        => 'nullable|array|max:5',
            'evidence.*'      => 'file|max:51200|mimes:jpg,jpeg,png,mp4,mov,mp3,aac,pdf,doc,docx',
        ];
    }

    public function messages(): array
    {
        return [
            'category.required'    => 'Please select a crime category.',
            'category.in'          => 'Invalid category selected.',
            'description.required' => 'Please describe what happened.',
            'description.min'      => 'Description must be at least 20 characters.',
            'district.required'    => 'Please select a district.',
            'files.max'            => 'Maximum 5 files allowed.',
            'files.*.max'          => 'Each file must be under 50MB.',
            'files.*.mimes'        => 'Allowed: images, video, audio, pdf, doc.',
            'evidence.max'         => 'Maximum 5 files allowed.',
            'evidence.*.max'       => 'Each file must be under 50MB.',
            'evidence.*.mimes'     => 'Allowed: images, video, audio, pdf, doc.',
        ];
    }
}