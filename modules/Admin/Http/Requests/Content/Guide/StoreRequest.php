<?php

namespace Modules\Admin\Http\Requests\Content\Guide;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Category\Models\Category;
use Modules\Guide\Domains\Guide;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [

        ];
    }

    public function save()
    {
        $guide = Guide::create(
                    $this->url,
                    $this->text,
                    $this->position,
                    $this->cover,
                    $this->duration,
                    $this->status
                );
        //Sync category
        if ($this->categories) {
            $category = Category::whereIn('id', $this->categories)->get();
            $guide->categories()->sync($category);
        }
        $guide->save();

    }
}
