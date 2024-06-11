<?php

namespace App\Http\Requests;

use App\Models\ExchangePoint;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the User is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id=$this->route('id');
        $user=User::find($id);
        $account_id=null;
        if($user){
            $account_id=$user->account_id;
        }
        return [
            'email' => 'unique:accounts,email,' . $account_id,
        ];
    }

    public function messages(): array
    {
        return[

            'email.unique' => 'يوجد حساب بهذا الايميل',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'fail',
            'message' => 'يوجد حساب بهذا البريد الإلكتروني',
        ]));    }
}
