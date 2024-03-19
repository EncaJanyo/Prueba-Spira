<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

	public function authorize()
	{
		return true;
	}


	public function rules()
	{
		$rules = [
			'name' => ['required', 'string'],
			'last_name' => ['required', 'string'],
			'phone' => ['required', 'numeric'],
			'email' => ['required', 'email'],
			'password' => ['confirmed', 'string', 'min:8'],
		];

		if ($this->method() == 'POST') {
			array_push($rules['email'], 'unique:users,email');
			array_push($rules['password'], 'required');
		} else {
			array_push($rules['email'], 'unique:users,email,' . $this->id);
			array_push($rules['password'], 'nullable');
		}

		if ($this->path() != 'api/register') {
			$rules['role'] = ['required', 'string', 'in:admin,student'];
		}

		return $rules;
	}

	public function messages()
	{
		return [
			'name.required' => 'El nombre es requerido',
			'name.string' => 'El nombre debe de ser valido',
            'last_name.required' => 'El apellido es requerido',
			'last_name.string' => 'El apellido debe de ser valido',
            'phone.required' => 'El numero es requerido',
			'phone.string' => 'El numero debe de ser valido',
            'email.required' => 'El correo es requerido',
			'email.email' => 'El correo debe de ser valido',
            'password.required' => 'La contraseña es requerida',
			'password.min' => 'La contraseña debe contener mas de 8 caracteres',
		];
	}
}
