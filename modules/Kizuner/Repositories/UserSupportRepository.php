<?php

namespace Modules\Kizuner\Repositories;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Contracts\UserSupportRepositoryInterface;
use Modules\Kizuner\Models\Skill;
use Modules\Kizuner\Models\UserSupport;

class UserSupportRepository implements UserSupportRepositoryInterface
{

    public function get(string $id)
    {
        $userSupport = UserSupport::find($id)->get();

        if (!$userSupport) {
            throw new ModelNotFoundException('User support does not exist');
        }
        return $userSupport;
    }

    public function getList(int $perPage, bool $isSystem)
    {
        if ($isSystem) {
            return UserSupport::where('admin', $isSystem)->paginate($perPage);
        }
        return UserSupport::paginate($perPage);
    }

    public function create($data)
    {
        $userSupport = new UserSupport($data);



        $userSupport->save();

        return $userSupport->refresh();
    }

    public function update($data)
    {
        return UserSupport::find($data['id'])->update($data);
    }

    public function delete($id)
    {
        return UserSupport::destroy($id);
    }

    public function search($field, $keyword, $suggest, $perPage)
    {
        $userSupports =  UserSupport::where($field, 'like', '%' . $keyword . '%')
                    ->where('admin', true);

        if ($suggest == true) {
            $userSupports->where('suggest', true);
        }
        return $userSupports->paginate($perPage);
    }
}
