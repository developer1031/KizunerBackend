<?php

namespace Modules\Admin\Http\Controllers\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Category\Models\Category;
use Modules\Kizuner\Models\Skill;
use Yajra\DataTables\Facades\DataTables;

class CategoryController
{
    public function edit(string $id)
    {
        $category = Category::find($id);
        return view('category::index')->with('category', $category);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $value = $request->name;
        $category = Category::find($id);
        $category->name = $value;
        $category->save();
        return redirect()->route('admin.category.index');
    }

    public function store(Request $request)
    {
        $name = $request->name;

        //Check skill exist
        $category = Category::where('name', $name)->first();

        if ($category) {
            return redirect()
                ->back()
                ->withError('Category duplicate');
        }

        Category::create([
            'name'  => $name
        ]);
        return redirect()
                    ->back()
                    ->withSuccess('Add Category successful');
    }

    public function index()
    {
        $category = new Category();
        return view('category::index')->with(['category' => $category]);
    }

    public function data()
    {
        $category = Category::query();
        return DataTables::eloquent($category->orderBy('name'))
            ->editColumn('edit', function($category) {
                return route('admin.category.edit', ['id' => $category->id]);
            })
            ->editColumn('delete', function($category) {
                return route('admin.category.delete', ['id' => $category->id]);
            })
            ->make(true);
    }

    public function specialityDataDasboard() {
        /*
        $skills = Skill::withCount('hangouts')
            ->withCount('helps', function($query) {})
            ->orderBy('hangouts_count', 'desc')->orderBy('helps_count', 'desc');
        */

        $skills = Skill::withCount(['hangouts', 'helps' => function($query) {
            $query->where('is_fake', '!=', 1 );
            $query->orWhereNull('is_fake');
        }])->orderBy('hangouts_count', 'desc')->orderBy('helps_count', 'desc');

        return DataTables::eloquent($skills->orderBy('name'))->make(true);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->back()->withSuccess('Category successful!');
    }
}
