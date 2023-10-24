<?php

namespace Modules\Admin\Http\Controllers\ChatLocation;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Http\Requests\ChatLocation\StoreRequest;
use Modules\Admin\Http\Requests\ChatLocation\UpdateRequest;
use Modules\Admin\Http\Requests\Package\UpdateRequest as PackageUpdateRequest;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Events\RoomDeletedEvent;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Kizuner\Models\Status;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;
use Yajra\DataTables\DataTables;

class ChatLocationController
{
    public function data()
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);

        return DataTables::of($roomManager->where('type', RoomEntity::TYPE_LOCATION)->get())
            ->editColumn('edit', function($chat_intent) {
                return route('admin.chat-location.edit', ['id' => $chat_intent->id]);
            })
            ->editColumn('delete', function($chat_intent) {
                return route('admin.chat-location.delete', ['id' => $chat_intent->id]);
            })
            ->make(true);
    }

    public function index()
    {
        $chat_location = EntityManager::create(RoomEntity::class);
        return view('chat-location::index')->with('chat_location', $chat_location);
    }

    public function store(StoreRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.chat-location.index'))->withSuccess('Add new Location successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function edit(string $id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $chat_location = $roomManager->where('id', $id)->where('status', 'active')->first();
        return view('chat-location::index')->with('chat_location', $chat_location);
    }

    public function update(UpdateRequest $request, string $id)
    {
        if ($request->validated()) {
            $request->save($id);
            return redirect(route('admin.chat-location.index'))->withSuccess('Update Location successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function destroy(string $id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $check = $roomManager->destroy($id);

        // Dispatch Event to Clean Database After
        if ($check) event(New RoomDeletedEvent($id));

        return redirect()->back()->withSuccess('Delete successful!');
    }

}
