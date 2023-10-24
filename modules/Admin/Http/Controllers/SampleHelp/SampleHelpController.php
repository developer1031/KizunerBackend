<?php

namespace Modules\Admin\Http\Controllers\SampleHelp;

use App\Imports\SampleHelpHangoutImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Http\Requests\ChatLocation\StoreRequest;
use Modules\Admin\Http\Requests\ChatLocation\UpdateRequest;
use Modules\Admin\Http\Requests\Package\UpdateRequest as PackageUpdateRequest;
use Modules\Category\Contracts\CategoryRepositoryInterface;
use Modules\Category\Models\Category;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Events\RoomDeletedEvent;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Helps\Models\SampleHelp;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Media;
use Modules\Kizuner\Models\Skill;
use Modules\Kizuner\Models\Status;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;
use Modules\Upload\Contracts\UploadRepositoryInterface;
use Modules\Upload\Models\UploadTrash;
use Modules\Upload\Services\UploadManager;
use Yajra\DataTables\DataTables;

class SampleHelpController
{
    /** @var MediaRepositoryInterface */
    private $mediaRepository;
    private $categoryRepository;
    private $skillRepository;
    private $uploadRepository;

    public function __construct(
        MediaRepositoryInterface $mediaRepository,
        CategoryRepositoryInterface $categoryRepository,
        SkillRepositoryInterface $skillRepository,
        UploadRepositoryInterface $uploadRepository
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->categoryRepository = $categoryRepository;
        $this->skillRepository = $skillRepository;
        $this->uploadRepository = $uploadRepository;
    }

    public function data()
    {
        $disk = \Storage::disk('gcs');
        return DataTables::of(SampleHelp::where('type', 'help'))
            ->addColumn('image', function($sample) use ($disk) {
                return ($sample->media) ?  '<img src="'. $disk->url($sample->media->path) .'">' : null;
            })
            ->editColumn('edit', function($sample) {
                return route('admin.sample-help.edit', ['id' => $sample->id]);
            })
            ->editColumn('delete', function($sample) {
                return route('admin.sample-help.delete', ['id' => $sample->id]);
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function index()
    {

//        dd($sampleHelp = SampleHelp::first());
//
//
//        $id = '699f66cf-e4a8-40ec-bb92-bc6cc53ce6fc';
//
//        $sampleHelpId = DB::table('skillables')
//            ->where('skill_id', $id)
//            ->where('skillable_type', SampleHelp::class)
//            ->orderByRaw('RAND()')
//            ->first();
//
//        $sampleHelp = SampleHelp::find($sampleHelpId->skillable_id);
//
//        dd($sampleHelp);
//
//
//        $sampleHelp = SampleHelp::whereHas('skills', function($query) use ($id) {
//            $query->where('id', $id);
//        })->get();
//
//        dd($sampleHelp);


        $sampleHelp = new SampleHelp();
        $skills = Skill::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $sample_specialities = $sample_categories = [];

        return view('sample-help::index', compact('skills', 'categories', 'sample_specialities', 'sample_categories'))->with('sampleHelp', $sampleHelp);
    }

    public function store(UploadManager $uploadManager, Request $request)
    {
        $data = $request->only('title', 'description');

        $data['type'] = 'help';
        $sampleHelp = new SampleHelp($data);
        if($sampleHelp->save()) {
            $sampleHelp->skills()->sync($request->speciality);
            $sampleHelp->categories()->sync($request->category);

            if ($request->file('file')) {
                /** Save Media */
                $response = $uploadManager->uploadFileResponseUploadObject($request);
                $media = $this->mediaRepository->update($response->id, ['type' => 'sample_help_hangout_cover']);
                $sampleHelp->media()->save($media);
            }
            return redirect(route('admin.sample-help.index'))->withSuccess('Add new successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function edit(string $id)
    {
        $skills = Skill::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $sampleHelp = SampleHelp::where('id', $id)->first();

        $sample_specialities = $sampleHelp->skills->pluck('id')->toArray();
        $sample_categories = $sampleHelp->categories->pluck('id')->toArray();

        $disk = \Storage::disk('gcs');
        $thumb = ($sampleHelp->media) ? $disk->url($sampleHelp->media->thumb) : null;

        return view('sample-help::index', compact('skills', 'sample_specialities', 'categories', 'sample_categories', 'thumb'))->with('sampleHelp', $sampleHelp);
    }

    public function update(UploadManager $uploadManager, Request $request, string $id)
    {
        $sampleHelp = SampleHelp::where('id', $id)->first();
        $sampleHelp->title = $request->title;
        $sampleHelp->description = $request->description;

        if($sampleHelp->save()) {
            $sampleHelp->skills()->sync($request->speciality);
            $sampleHelp->categories()->sync($request->category);

            if ($request->file('file')) {
                /** Save Media */
                $response = $uploadManager->uploadFileResponseUploadObject($request);
                $media = $this->mediaRepository->update($response->id, ['type' => 'sample_help_hangout_cover']);
                $sampleHelp->media()->delete();
                $sampleHelp->media()->save($media);
            }
            return redirect(route('admin.sample-help.index'))->withSuccess('Updated successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function destroy(string $id)
    {
        $sampleHelp = SampleHelp::where('id', $id)->first();
        if($sampleHelp)
            $check = $sampleHelp->delete();
        return redirect(route('admin.sample-help.index'))->withSuccess('Delete successful!');
    }

    public function import(Request $request) {
        Excel::import(new SampleHelpHangoutImport($this->categoryRepository, $this->skillRepository, $this->uploadRepository, $this->mediaRepository, 'help'), $request->file('file'));
        return redirect(route('admin.sample-help.index'))->withSuccess('Import successful!');
    }

    /*
     * Sample Hangout
     */

    public function hangoutIndex() {
        $sampleHelp = new SampleHelp();
        $skills = Skill::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $sample_specialities = $sample_categories = [];

        return view('sample-help::index-hangout', compact('skills', 'categories', 'sample_specialities', 'sample_categories'))
            ->with('sampleHelp', $sampleHelp);
    }

    public function dataHangout()
    {
        $disk = \Storage::disk('gcs');
        return DataTables::of(SampleHelp::where('type', 'hangout'))
            ->addColumn('image', function($sample) use ($disk) {
                return ($sample->media) ?  '<img src="'. $disk->url($sample->media->path) .'">' : null;
            })
            ->editColumn('edit', function($sample) {
                return route('admin.sample-hangout.edit', ['id' => $sample->id]);
            })
            ->editColumn('delete', function($sample) {
                return route('admin.sample-hangout.delete', ['id' => $sample->id]);
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function hangoutStore(UploadManager $uploadManager, Request $request)
    {
        $data = $request->only('title', 'description');
        $data['type'] = 'hangout';
        $sampleHelp = new SampleHelp($data);
        if($sampleHelp->save()) {
            $sampleHelp->skills()->sync($request->speciality);
            $sampleHelp->categories()->sync($request->category);

            if ($request->file('file')) {
                /** Save Media */
                $response = $uploadManager->uploadFileResponseUploadObject($request);
                $media = $this->mediaRepository->update($response->id, ['type' => 'sample_help_hangout_cover']);
                $sampleHelp->media()->save($media);
            }
            return redirect(route('admin.sample-hangout.index'))->withSuccess('Add new successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function hangoutEdit(string $id)
    {
        $skills = Skill::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $sampleHelp = SampleHelp::where('id', $id)->first();

        $sample_specialities = $sampleHelp->skills->pluck('id')->toArray();
        $sample_categories = $sampleHelp->categories->pluck('id')->toArray();

        $disk = \Storage::disk('gcs');
        $thumb = ($sampleHelp->media) ? $disk->url($sampleHelp->media->thumb) : null;

        return view('sample-help::index-hangout', compact('skills', 'sample_specialities', 'categories', 'sample_categories', 'thumb'))->with('sampleHelp', $sampleHelp);
    }

    public function hangoutUpdate(UploadManager $uploadManager, Request $request, string $id)
    {
        $sampleHelp = SampleHelp::where('id', $id)->first();
        $sampleHelp->title = $request->title;
        $sampleHelp->description = $request->description;

        if($sampleHelp->save()) {
            $sampleHelp->skills()->sync($request->speciality);
            $sampleHelp->categories()->sync($request->category);
            if ($request->file('file')) {
                /** Save Media */
                $response = $uploadManager->uploadFileResponseUploadObject($request);
                $media = $this->mediaRepository->update($response->id, ['type' => 'sample_help_hangout_cover']);
                $sampleHelp->media()->delete();
                $sampleHelp->media()->save($media);
            }
            return redirect(route('admin.sample-hangout.index'))->withSuccess('Updated successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function hangoutDestroy(string $id)
    {
        $sampleHelp = SampleHelp::where('id', $id)->first();
        if($sampleHelp)
            $check = $sampleHelp->delete();
        return redirect(route('admin.sample-hangout.index'))->withSuccess('Delete successful!');
    }

    public function importHangout(Request $request) {
        Excel::import(new SampleHelpHangoutImport($this->categoryRepository, $this->skillRepository, $this->uploadRepository, $this->mediaRepository, 'hangout'), $request->file('file'));
        return redirect(route('admin.sample-hangout.index'))->withSuccess('Import successful!');
    }
}
