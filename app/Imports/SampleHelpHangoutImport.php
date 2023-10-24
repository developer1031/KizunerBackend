<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Category\Contracts\CategoryRepositoryInterface;
use Modules\Category\Models\Category;
use Modules\Helps\Models\SampleHelp;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Skill;
use Modules\Upload\Contracts\UploadPath;
use Modules\Upload\Contracts\UploadRepositoryInterface;
use Modules\Upload\Services\UploadManager;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\Mime\Exception\ExceptionInterface;

class SampleHelpHangoutImport implements ToCollection
{

    private $categoryRepository;
    private $skillRepository;
    private $uploadRepository;
    private $mediaRepository;
    private $type = 'help';

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SkillRepositoryInterface $skillRepository,
        UploadRepositoryInterface $uploadRepository,
        MediaRepositoryInterface $mediaRepository,
        $type = 'help'
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->skillRepository = $skillRepository;
        $this->uploadRepository = $uploadRepository;
        $this->mediaRepository = $mediaRepository;
        $this->type = $type;
    }


    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            try {
                if($key > 0) {
                    $data = [
                        'title' => $row[0],
                        'description' => $row[1],
                        'type' => $this->type
                    ];
                    $sampleHelp = new SampleHelp($data);

                    if($sampleHelp->save()) {
                        //Category
                        $categoryCollection = collect();
                        $categories = explode(',', $row[2]);
                        foreach ($categories as $key => $value) {
                            $category = Category::where('name', trim($value))->first();
                            if (!$category) {
                                $category = $this->categoryRepository->create([
                                    'name'      => trim($value),
                                    'suggest'   => false,
                                    'system'    => false
                                ]);
                            }
                            $categoryCollection->push($category);
                        }
                        $categoryCollection->each(function ($item) use ($sampleHelp) {
                            $sampleHelp->categories()->save($item);
                        });

                        //Specialites
                        $skillCollection = collect();
                        $specialities = explode(',', $row[3]);
                        foreach ($specialities as $key => $value) {
                            $skill = Skill::where('name', trim($value))->first();
                            if (!$skill) {
                                $skill = $this->skillRepository->create([
                                    'name'      => trim($value),
                                    'suggest'   => false,
                                    'system'    => false
                                ]);
                            }
                            $skillCollection->push($skill);
                        }
                        $skillCollection->each(function ($item) use ($sampleHelp) {
                            $sampleHelp->skills()->save($item);
                        });

                        //Image
                        $image_url   = trim($row[4]);
                        try {
                            if($image_url) {
                                $disk = \Storage::disk('gcs');
                                $fileName = pathinfo(time(), PATHINFO_FILENAME);
                                $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '.jpg';

                                $originalRs = file_get_contents($image_url);
                                $disk->put($saveOriginal, $originalRs);

                                $type = 'sample_help_hangout_cover';
                                $upload = $this->uploadRepository->create([
                                    'path' => $saveOriginal,
                                    'thumb' => $saveOriginal,
                                    'type' => $type
                                ]);
                                $media = $this->mediaRepository->update($upload->id, ['type' => 'sample_help_hangout_cover']);
                                $sampleHelp->media()->save($media);
                            }
                        }
                        catch (\Exception $e) {}
                    }
                }
            }
            catch (\Exception $e) {}
        }
    }
}
