<?php

namespace Modules\Admin\Http\Controllers\Content;

use Composer\DependencyResolver\Request;
use Modules\Category\Models\Category;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Guide\Domains\Entities\GuideEntity;
use Modules\Guide\Domains\Guide;
use Modules\Admin\Http\Requests\Content\Guide\StoreRequest as GuideStoreRequest;
use Modules\Admin\Http\Requests\Content\Guide\UpdateRequest as GuideUpdateRequest;

class GuideController
{
    public function index()
    {
        //$categories = Category::where('type', 'video')->get();
        $categories = Category::whereNull('type')->orderBy('name')->get();
        $guide = EntityManager::create(GuideEntity::class);
        $category_id = '';
        return view('content::guide.index', compact('categories', 'category_id'))->with('guide', $guide);
    }

    public function store(GuideStoreRequest $request)
    {
        $request->save();
        return redirect(route('admin.content.guide.index'))->withSuccess("Save new Video Guide Successful");
    }

    public function edit(string $id)
    {
        //$categories = Category::where('type', 'video')->get();
        $categories = Category::whereNull('type')->orderBy('name')->get();
        $guide = Guide::find($id);
        if ($guide) {


            $categories_selected = $guide->categories;

            $category_ids = array();
            foreach ($categories_selected as $category) {
                array_push($category_ids, $category->id);
            }

            return view('content::guide.index', compact('categories', 'category_ids'))->with('guide', $guide);
        }
        return redirect(route('admin.content.guide.index'));
    }

    public function update(GuideUpdateRequest $request)
    {
        $request->save();
        return redirect(route('admin.content.guide.index'));
    }

    public function destroy(string $id)
    {
        Guide::delete($id);
        return redirect(route('admin.content.guide.index'));
    }

    public function data()
    {
        return \Yajra\DataTables\Facades\DataTables::eloquent(GuideEntity::query())->make(true);
    }

    public function category() {
        //$category = Category::where('type', 'video')->get();
        $category = new Category();
        $data = [
            'category' => $category
        ];
        return view('content::guide.category', $data);
    }

    public function categoryData() {
        return \Yajra\DataTables\Facades\DataTables::eloquent(Category::where('type', 'video'))->make(true);
    }

    public function createCategory(\Illuminate\Http\Request $request) {
        if($request->has('name')) {
            $name = $request->name;
            $category = Category::create(['name' => $name, 'type' => 'video']);
        }
        return redirect(route('admin.content.guide.category'));
    }

    public function categoryEdit($id) {
        $category = Category::find($id);
        $data = [
            'category' => $category
        ];
        return view('content::guide.category', $data);
    }

    public function categoryUpdate($id, \Illuminate\Http\Request $request) {
        $category = Category::find($id);
        $category->name = $request->name;
        $category->save();
        return redirect(route('admin.content.guide.category'));
    }

    public function categoryDelete($id) {
        $category = Category::find($id);
        $category->delete();
        return redirect(route('admin.content.guide.category'));
    }
}
