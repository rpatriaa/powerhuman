<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:employees,name',
            'email' => 'required|email|unique:employees,email',
            'gender' => 'required|in:male,female',
            'age' => 'required|integer',
            'phone' => 'required|string|max:15|unique:employees,phone',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'team_id' => 'required|exists:teams,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
