<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class giftCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function onCreate()
    {
        return [
            'name' => 'required|string|between:2,100',
            'card_number' => 'required|digits:16|unique:gift_cards',
            'value' => 'required',
        ];
    }

    protected function onUpdate()
    {
        return [
            'name' => 'required|string|between:2,100',
            'card_number'    => [
                'required', 'digits:16',
                Rule::unique('gift_cards')->ignore($this->giftCard),
            ],
            'value' => 'required',
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
