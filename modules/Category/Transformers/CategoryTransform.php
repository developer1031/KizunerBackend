<?php

namespace Modules\Category\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Category\Models\Category;
use Modules\Kizuner\Models\Skill;

class CategoryTransform extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'admin' => $category->admin == 0 ? false : true,
            'suggest' => $category->suggest == 0 ? false : true,
        ];
    }
}
