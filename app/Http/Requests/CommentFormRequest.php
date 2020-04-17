<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'string | max: 2000',
            'images.*' => 'mimes:jpeg,png,jpg | max:1000',
            'postId' => 'integer | exists:posts,id',
            'commentId' => 'integer | exists:comments,id'
        ];
    }
}
