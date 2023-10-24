<?php

namespace Modules\KizunerApi\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Skill;
use Modules\KizunerApi\Http\Requests\Skill\SkillCreateRequest;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\KizunerApi\Transformers\SkillTransform;
use Modules\KizunerApi\Transformers\UserTransform;
use Symfony\Component\HttpFoundation\Response;

class SkillManager
{
    /** @var SkillRepositoryInterface */
    private $skillRepository;

    /**
     * SkillManager constructor.
     * @param SkillRepositoryInterface $skillRepository
     */
    public function __construct(SkillRepositoryInterface $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    /**
     * @param SkillCreateRequest $request
     * @return \Spatie\Fractal\Fractal
     * @throws \Exception
     */
    public function addSkills(SkillCreateRequest $request)
    {
        $data = $request->all('skills');
        $skillCollection = collect();
        foreach ($data['skills'] as $key => $value) {
            $skill = Skill::where('name', $value)->first();
            if (!$skill) {
                $skill = $this->skillRepository->create([
                    'name'      => $value,
                    'suggest'   => false,
                    'system'    => false
                ]);
            }
            $skillCollection->push($skill);
        }

        if($request->has('is_update_profile') && $request->is_update_profile) {
            $user = $request->user();
            $skillCollection->each(function ($item) use ($user) {
                $user->skills()->save($item);
            });
        }

        return fractal($request->user(), new UserTransform(null, $skillCollection));
    }

    /**
     * @param string $keyword
     * @return \Spatie\Fractal\Fractal
     */
    public function search()
    {
        $keyword = app('request')->input('query');
        $perPage = app('request')->input('per_page');

        if ($perPage) {
            $perPage = 5;
        }

        $suggest = false;

        $type = app('request')->input('type');

        if ($type == 'suggest') {
            $suggest = true;
        }

        $skills = $this->skillRepository->search('name', $keyword, $suggest, $perPage);
        return fractal($skills, new SkillTransform());
    }

    /**
     * @param Request $request
     * @param bool $isAdmin
     * @return \Spatie\Fractal\Fractal
     */
    public function getSkills(Request $request, bool $isAdmin)
    {

        if (!$perPage = $request->get('per_page')) {
            $perPage = 5;
        }

        $skills = $this->skillRepository->getList($perPage, $isAdmin);
        return fractal($skills, new SkillTransform());
    }

    /**
     * @param string $id
     * @return \Spatie\Fractal\Fractal
     */
    public function getHangouts(string $ids)
    {
        $idsSet = explode(',', $ids);
        $hangoutList = $this->skillRepository->getHangoutsByIds($idsSet);
        return fractal($hangoutList, new HangoutTransform());
    }
}
