<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{

	public function authorize()
	{
		return true;
	}


	public function rules()
	{
		$rules = [
			'name' => ['required', 'string'],
			'hour' => ['required', 'numeric'],
		];

		return $rules;
	}

	public function messages()
	{
		return [
			'name.required' => 'El nombre es requerido',
			'name.string' => 'El nombre debe de ser valido',
            'hour.required' => 'La intesidad horaria es requerida',
			'hour.numeric' => 'La intesidad horaria debe de ser valida',
		];
	}
}
