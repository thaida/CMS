<?php namespace App\Http\Requests;

use App\Models\Film;

class FilmRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = $this->film ? ',' . $this->film : '';
		return [
			'title' => 'required|max:255',
			'summary' => 'required|max:65000'
			//'link' => 'required|max:30',
			//'slug' => 'required|unique:posts,slug' . $id,
		];
	}

}