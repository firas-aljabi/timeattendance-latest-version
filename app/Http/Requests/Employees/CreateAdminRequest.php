<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Statuses\GenderStatus;
use App\Statuses\MaterialStatus;
use Illuminate\Validation\Rule;

class CreateAdminRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            "email" => "required|email|regex:/^[a-zA-Z0-9._%+-]{1,16}@(?!.*\*)/",
            "password" => "required|min:8|max:24|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,24}$/",
            "work_email" => "required|email|regex:/^[a-zA-Z0-9._%+-]{1,16}@(?!.*\*)/",
            'mobile' => 'sometimes|unique:users,mobile',
            'phone' => 'sometimes|unique:users,phone',
            "company_id" => 'required',
            'nationalitie_id' => 'required|exists:nationalities,id',
            'birthday_date' => 'required|date',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'address' => 'nullable|string',
            'serial_number' => 'required|unique:users,serial_number',
            "gender" => ["nullable", Rule::in(GenderStatus::$statuses)],
            'branch' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
