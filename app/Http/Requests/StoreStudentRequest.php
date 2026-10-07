<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|max:20|unique:students,nis',
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'nik' => 'nullable|string|max:20',
            'school_code' => 'nullable|string|max:50',
            'district_code' => 'nullable|string|max:50',
            'city_code' => 'nullable|string|max:50',
            'province_code' => 'nullable|string|max:50',
            'student_number' => 'nullable|string|max:20',
            'name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'citizenship' => 'nullable|string|max:50',
            'child_position' => 'nullable|integer',
            'siblings_biological' => 'nullable|integer',
            'siblings_step' => 'nullable|integer',
            'siblings_adopted' => 'nullable|integer',
            'daily_language' => 'nullable|string|max:50',
            'blood_type' => 'nullable|string|max:5',
            'address' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:20',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'village' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'living_with' => 'nullable|string|max:50',
            'distance_to_school' => 'nullable|string|max:30',
            'diseases' => 'nullable|string',
            'immunizations' => 'nullable|string',

            'father_name' => 'nullable|string|max:100',
            'father_nik' => 'nullable|string|max:20',
            'father_birth_place' => 'nullable|string|max:100',
            'father_birth_date' => 'nullable|date',
            'father_education' => 'nullable|string|max:50',
            'father_job' => 'nullable|string|max:100',
            'father_address' => 'nullable|string',
            'father_phone' => 'nullable|string|max:30',
            'father_citizenship' => 'nullable|string|max:50',

            'mother_name' => 'nullable|string|max:100',
            'mother_nik' => 'nullable|string|max:20',
            'mother_birth_place' => 'nullable|string|max:100',
            'mother_birth_date' => 'nullable|date',
            'mother_education' => 'nullable|string|max:50',
            'mother_job' => 'nullable|string|max:100',
            'mother_address' => 'nullable|string',
            'mother_phone' => 'nullable|string|max:30',
            'mother_citizenship' => 'nullable|string|max:50',

            'guardian_name' => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:50',
            'guardian_birth_place' => 'nullable|string|max:100',
            'guardian_birth_date' => 'nullable|date',
            'guardian_education' => 'nullable|string|max:50',
            'guardian_job' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',
            'guardian_phone' => 'nullable|string|max:30',
            'guardian_citizenship' => 'nullable|string|max:50',

            'student_origin' => 'nullable|string|max:100',
            'scholarship_type' => 'nullable|string|max:100',
            'graduation_year' => 'nullable|string|max:20',
            'graduation_certificate_number' => 'nullable|string|max:100',
            'continued_school' => 'nullable|string|max:150',
            'transfer_left_grade' => 'nullable|string|max:50',
            'transfer_to_school' => 'nullable|string|max:150',
            'transfer_to_grade' => 'nullable|string|max:50',
            'transfer_date' => 'nullable|date',
            'leaving_date' => 'nullable|date',
            'leaving_reason' => 'nullable|string',

            'previous_school_name' => 'nullable|string|max:100',
            'certificate_date_number' => 'nullable|string|max:100',
            'transfer_from_school' => 'nullable|string|max:100',
            'transfer_from_grade' => 'nullable|string|max:50',
            'transfer_accepted_date' => 'nullable|date',
            'transfer_letter_number' => 'nullable|string|max:100',

            'physiques' => 'nullable|array',
            'healths' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:Aktif,Pindah,Lulus,Alumni,Nonaktif',
            'class_id' => 'nullable|exists:classes,id',
        ];
    }
}
