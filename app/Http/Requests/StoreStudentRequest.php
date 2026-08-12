<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization akan diatur via middleware/policy nanti
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nis'                    => 'required|string|max:20|unique:students,nis',
            'nisn'                   => 'required|string|max:20|unique:students,nisn',
            'nik'                    => 'nullable|string|max:20',
            'school_code'            => 'nullable|string|max:50',
            'district_code'          => 'nullable|string|max:50',
            'city_code'              => 'nullable|string|max:50',
            'province_code'          => 'nullable|string|max:50',
            'student_number'         => 'nullable|string|max:20',
            'name'                   => 'required|string|max:100',
            'nickname'               => 'nullable|string|max:50',
            'gender'                 => 'required|in:L,P',
            'birth_place'            => 'nullable|string|max:100',
            'birth_date'             => 'nullable|date',
            'religion'               => 'nullable|string|max:30',
            'citizenship'            => 'nullable|string|max:50',
            'child_position'         => 'nullable|integer',
            'siblings_biological'    => 'nullable|integer',
            'siblings_step'          => 'nullable|integer',
            'siblings_adopted'       => 'nullable|integer',
            'daily_language'         => 'nullable|string|max:50',
            'blood_type'             => 'nullable|string|max:5',
            'address'                => 'nullable|string',
            'rt_rw'                  => 'nullable|string|max:20',
            'village'                => 'nullable|string|max:50',
            'district'               => 'nullable|string|max:50',
            'city'                   => 'nullable|string|max:50',
            'province'               => 'nullable|string|max:50',
            'postal_code'            => 'nullable|string|max:10',
            'phone_number'           => 'nullable|string|max:20',
            'living_with'            => 'nullable|string|max:50',
            'distance_to_school'     => 'nullable|string|max:30',
            'kk_number'              => 'nullable|string|max:20',
            'father_name'            => 'nullable|string|max:100',
            'father_nik'             => 'nullable|string|max:20',
            'father_education'       => 'nullable|string|max:50',
            'father_job'             => 'nullable|string|max:100',
            'mother_name'            => 'nullable|string|max:100',
            'mother_nik'             => 'nullable|string|max:20',
            'mother_education'       => 'nullable|string|max:50',
            'mother_job'             => 'nullable|string|max:100',
            'guardian_name'          => 'nullable|string|max:100',
            'guardian_relation'      => 'nullable|string|max:50',
            'guardian_education'     => 'nullable|string|max:50',
            'guardian_job'           => 'nullable|string|max:100',
            'guardian_phone'         => 'nullable|string|max:20',
            'previous_school_name'   => 'nullable|string|max:100',
            'certificate_date_number' => 'nullable|string|max:100',
            'transfer_from_school'   => 'nullable|string|max:100',
            'transfer_from_grade'    => 'nullable|string|max:50',
            'transfer_accepted_date' => 'nullable|date',
            'transfer_letter_number' => 'nullable|string|max:100',
            'physiques'              => 'nullable|array',
            'physiques.*.academic_year_id' => 'nullable|exists:academic_years,id',
            'physiques.*.semester_id' => 'nullable|exists:semesters,id',
            'physiques.*.height'     => 'nullable|numeric|min:0',
            'physiques.*.weight'     => 'nullable|numeric|min:0',
            'healths'                => 'nullable|array',
            'healths.*.academic_year_id' => 'nullable|exists:academic_years,id',
            'healths.*.semester_id'  => 'nullable|exists:semesters,id',
            'healths.*.hearing'      => 'nullable|string|max:100',
            'healths.*.vision'       => 'nullable|string|max:100',
            'healths.*.teeth'        => 'nullable|string|max:100',
            'healths.*.notes'        => 'nullable|string',
            'photo'                  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'                 => 'required|in:Aktif,Pindah,Lulus,Alumni,Nonaktif',
            'class_id'               => 'nullable|exists:classes,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nis.required'    => 'Nomor Induk Siswa wajib diisi.',
            'nis.unique'      => 'Nomor Induk Siswa sudah terdaftar.',
            'nisn.required'   => 'NISN wajib diisi.',
            'nisn.unique'     => 'NISN sudah terdaftar.',
            'name.required'   => 'Nama peserta didik wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in'       => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'status.required' => 'Status siswa wajib dipilih.',
            'status.in'       => 'Status siswa tidak valid.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'photo.image'     => 'File harus berupa gambar.',
            'photo.mimes'     => 'Format foto harus JPEG, PNG, atau JPG.',
            'photo.max'       => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
