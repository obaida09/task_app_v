<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function onCreate()
    {
        return [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ];
    }

    protected function onUpdate()
    {
        return [
            'name' => 'string|between:2,100',
            'email'    => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => 'nullable|string|confirmed|min:6',
        ];
    }

    public function rules()
    {
        return request()->isMethod('PUT') || request()->isMethod('patch') ?
        $this->onUpdate() : $this->onCreate();
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
