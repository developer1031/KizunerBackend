<?php

namespace Modules\Admin\Http\Controllers\Package;

use Modules\Admin\Http\Requests\Package\StoreRequest as PackageStoreRequest;
use Modules\Admin\Http\Requests\Package\UpdateRequest as PackageUpdateRequest;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;
use Yajra\DataTables\DataTables;

class PackageController
{
    public function index()
    {
        $package = EntityManager::create(PackageEntity::class);
        return view('package::index')->with('package', $package);
    }

    public function store(PackageStoreRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.package.index'))->withSuccess('Add new package successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function edit(string $id)
    {
        $package = Package::find($id);
        $package->price = Price::humanPrice($package->price);
        return view('package::index')->with('package', $package);
    }

    public function update(PackageUpdateRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.package.index'))->withSuccess('Update package successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function data()
    {
        $packages = EntityManager::getManager(PackageEntity::class);
        return DataTables::of($packages->query())
                        ->editColumn('edit', function($package) {
                            return route('admin.package.edit', ['id' => $package->id]);
                        })
                        ->make(true);
    }
}
