<?php

namespace Modules\Kizuner\Repositories;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Skill;

class SkillRepository implements SkillRepositoryInterface
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
        $skill = new Skill($data);

        $skill->save();
        return $skill;
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
        $skills =  Skill::where($field, 'like', '%' . $keyword . '%')
                    ->where('admin', true);

        if ($suggest == true) {
            $skills->where('suggest', true);
        }
        return $skills->paginate($perPage);
    }

    public function getHangoutsByIds(array $ids)
    {
        $skill = Skill::whereIn('id', $ids)->get();

        if (!$skill) {
            throw new ModelNotFoundException('Skill does not exist');
        }

        $hangoutCollection = collect();

        $skill->each(function ($item, $key) use ($hangoutCollection) {
            $skillHangout = $item->hangouts;
            $skillHangout->each(function ($item, $key) use ($hangoutCollection) {
                $hangoutCollection->push($item);
            });
        });
        return $hangoutCollection;
    }
}
