<?php

namespace App\Http\Requests\Employees;

use App\Statuses\MaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'material_status' => ['nullable', Rule::in(MaterialStatus::$statuses)],
            'housing_allowance' => 'nullable',
            'transportation_allowance' => 'nullable',
        ];
    }
}
