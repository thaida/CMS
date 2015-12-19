<?php namespace App\Http\Requests;

class CategoryCreateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required|max:100',
			'slug' => 'required|max:50'
		];
	}

}