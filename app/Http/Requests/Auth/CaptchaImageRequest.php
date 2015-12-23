<?php namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class CaptchaImageRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'mobile' => 'required', 'captcha' => 'required|captcha',
		];
	}

}
