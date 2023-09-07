<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

class CreateWeeklyHolidayRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'days' => 'required|array',
            'days.*.day' => 'required_if:days,!=,null',
        ];
    }
}
