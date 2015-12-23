<?php namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class RegisterRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'mobile' => 'required|numeric|min:10',
			//'email' => 'required|email|max:255|unique:users',
			//'password' => 'required|min:8|confirmed',
			'captcha' => 'required|captcha',
		];
	}

}
