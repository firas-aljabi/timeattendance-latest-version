<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Statuses\EmployeeStatus;
use App\Statuses\GenderStatus;
use App\Statuses\MaterialStatus;
use App\Statuses\PermissionType;
use App\Statuses\TimeSelect;
use App\Statuses\UserTypes;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            "email" => "required|email|max:255|regex:/^[a-zA-Z0-9._%+-]{1,16}[^*]{0,}@[^*]+$/",
            "password" => "required|min:8|max:24|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,24}$/",
            "work_email" => "nullable|email|max:255|regex:/^[a-zA-Z0-9._%+-]{1,16}[^*]{0,}@[^*]+$/",
            'mobile' => 'sometimes|unique:users,mobile',
            'phone' => 'sometimes|unique:users,phone',
            'nationalitie_id' => 'required|exists:nationalities,id',
            'birthday_date' => 'required|date',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'departement' => 'nullable|string',
            'position' => 'nullable|string',
            'address' => 'nullable|string',
            'guarantor' => 'nullable|string',
            'branch' => 'nullable|string',
            'skills' => 'nullable|string',
            'serial_number' => 'required|unique:users,serial_number',
            "gender" => ["nullable", Rule::in(GenderStatus::$statuses)],
            'status' => [Rule::in(EmployeeStatus::$statuses)],
            'start_job_contract' => 'required|date',
            'end_job_contract' => 'required|date',
            'end_visa' => 'nullable|date',
            'end_employee_sponsorship' => 'nullable|date',
            'end_municipal_card' => 'nullable|date',
            'end_health_insurance' => 'nullable|date',
            'end_employee_residence' => 'nullable|date',
            'leave_time' => [Rule::in(TimeSelect::$statuses), 'nullable'],
            'entry_time' => [Rule::in(TimeSelect::$statuses), 'nullable'],
            'end_passport' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_photo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'biography' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'visa' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'municipal_card' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'employee_residence' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'health_insurance' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'passport' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'employee_sponsorship' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'basic_salary' => 'required',
            'housing_allowance' => 'nullable',
            'hourly_annual' => 'sometimes',
            'daily_annual' => 'sometimes',
            'transportation_allowance' => 'nullable',
            'number_of_shifts' => 'nullable|integer|min:1',
            'shifts' => 'required_if:number_of_shifts,!=,null|array',
            'shifts.*.user_id' => 'required_if:shifts,null',
            'shifts.*.start_time' => 'required',
            'shifts.*.end_time' => 'required',
            'shifts.*.start_break_hour' => 'required',
            'shifts.*.end_break_hour' => 'required'
        ];
    }
}
