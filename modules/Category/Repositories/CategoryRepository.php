<?php

namespace Modules\Category\Repositories;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Category\Contracts\CategoryRepositoryInterface;
use Modules\Category\Models\Category;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Skill;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function get(string $id)
    {
        $skill = Skill::find($id)->get();

        if (!$skill) {
            throw new ModelNotFoundException('Skill does not exist');
        }
        return $skill;
    }

    public function getList(int $perPage, bool $isSystem)
    {
        if ($isSystem) {
            return Skill::where('admin', $isSystem)->paginate($perPage);
        }
        return Skill::paginate($perPage);
    }

    public function create($data)
    {
        $category = new Category($data);
        $category->save();
        return $category;
    }

    public function update($data)
    {
        return Skill::find($data['id'])->update($data);
    }

    public function delete($id)
    {
        return Skill::destroy($id);
    }

    public function search($field, $keyword, $suggest, $perPage)
    {
        $skills =  Category::where($field, 'like', '%' . $keyword . '%')
                    ->where('admin', true);

        if ($suggest == true) {
            $skills->where('suggest', true);
        }
        return $skills->paginate($perPage);
    }
}
