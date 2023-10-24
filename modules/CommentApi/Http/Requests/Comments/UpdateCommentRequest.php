<?php

namespace Modules\CommentApi\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Comment\Services\Facades\CommentAuth;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userId = Auth::user()->id;
        $id   = $this->route('id');

        if (CommentAuth::check($id, $userId)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body'  => 'required',
        ];
    }
}
