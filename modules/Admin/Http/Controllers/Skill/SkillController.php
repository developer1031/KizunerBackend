<?php

namespace Modules\Admin\Http\Controllers\Skill;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Kizuner\Models\Skill;
use Yajra\DataTables\Facades\DataTables;

class SkillController
{

    public function edit(string $id) {
        $skill = Skill::find($id);
        return view('skill::index')->with('skill', $skill);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $name = $request->name;
        $suggest = $request->suggest == 'on' ? 1 : 0;

        $skill = Skill::find($id);
        $skill->name = $name;
        $skill->suggest = $suggest;
        $skill->save();
        // return response()->json([
        //     'id'        => $skill->id,
        //     'suggest'   => $skill->suggest
        // ], Response::HTTP_OK);

        return redirect()
                    ->back()
                    ->withSuccess('Update specialities successful');
    }

    public function store(Request $request)
    {
        $name = $request->name;
        $suggest = $request->suggest;

        //Check skill exist
        $skill = Skill::where('name', $name)->first();

        if ($skill) {
            return redirect()
                ->back()
                ->withError('Specialities duplicate');
        }

        Skill::create([
            'admin' => true,
            'suggest' => $suggest == 'on' ? true : false,
            'name'  => $name
        ]);
        return redirect()
                    ->back()
                    ->withSuccess('Add specialities successful');
    }

    public function index()
    {
        $skill = new Skill();
        return view('skill::index')->with(['skill' => $skill]);
    }

    public function data()
    {
        $skill = Skill::query();
        return DataTables::eloquent($skill->orderBy('name'))
                    ->editColumn('edit', function($skill) {
                        return route('admin.skill.edit', ['id' => $skill->id]);
                    })
                    ->editColumn('delete', function($skill) {
                        return route('admin.skill.delete', ['id' => $skill->id]);
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

    public function delete(string $id)
    {
        $skill = Skill::find($id);
        $skill->delete();
        return redirect()->back()->withSuccess('Remove successful!');
    }
}
