<?php

namespace Modules\Category\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Category\Contracts\CategoryRepositoryInterface;
use Modules\Category\Http\Requests\CategoryCreateRequest;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryTransform;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Skill;
use Modules\KizunerApi\Http\Requests\Skill\SkillCreateRequest;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\KizunerApi\Transformers\SkillTransform;
use Modules\KizunerApi\Transformers\UserTransform;
use Symfony\Component\HttpFoundation\Response;

class CategoryManager
{
    /** @var  CategoryRepositoryInterface */
    private $categoryRepository;

    /**
     * SkillManager constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param  CategoryCreateRequest $request
     * @return \Spatie\Fractal\Fractal
     * @throws \Exception
     */
    public function addCategories(CategoryCreateRequest $request)
    {
        $data = $request->all('categories');

        $categoryCollection = collect();
        foreach ($data['categories'] as $key => $value) {

            $category = Category::where('name', $value)->first();
            if (!$category) {
                $category = $this->categoryRepository->create([
                    'name'      => $value,
                    'suggest'   => false,
                    'system'    => false
                ]);
            }
            $categoryCollection->push($category);
        }

        /*
        $user = $request->user();
        $categoryCollection->each(function ($item) use ($user) {
            $user->categories()->save($item);
        });
        */

        return fractal($request->user(), new UserTransform($categoryCollection));
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

        $categories = $this->categoryRepository->search('name', $keyword, $suggest, $perPage);
        return fractal($categories, new CategoryTransform());
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
