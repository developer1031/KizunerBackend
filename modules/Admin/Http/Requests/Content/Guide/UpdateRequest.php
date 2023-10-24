<?php

namespace Modules\Admin\Http\Requests\Content\Guide;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Category\Models\Category;
use Modules\Guide\Domains\Guide;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function save()
    {
        $guide = Guide::find($this->id);
        $guide->url = $this->url;
        $guide->text = $this->text;
        $guide->position = $this->position;
        $guide->cover = $this->cover;
        $guide->duration = $this->duration;
        $guide->status = $this->status;
        if ($this->categories) {
            $category = Category::whereIn('id', $this->categories)->get();
            $guide->categories()->sync($category);
        }
        $guide->save();
    }
}
