<?php
namespace Modules\Guide\Http\Transformers;

use League\Fractal\TransformerAbstract as Transformer;

class GuideTransformer extends Transformer
{
    public function transform($guide)
    {
        $guide_categories = $guide->categories()->get()->toArray();

        return [
            'id'        => $guide->id,
            'url'       => $guide->url,
            'text'      => $guide->text,
            'duration'  => $guide->duration,
            'cover'     => $guide->cover,
            'categories'  => $guide_categories
        ];
    }
}
