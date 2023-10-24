<?php

namespace Modules\Admin\Http\Controllers\ChatPublicGroup;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Http\Requests\ChatPublicGroup\StoreRequest;
use Modules\Admin\Http\Requests\ChatPublicGroup\UpdateRequest;
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

class ChatPublicGroupController
{
    public function data()
    {
        $disk = \Storage::disk('gcs');
        $roomManager = EntityManager::getManager(RoomEntity::class);

        return DataTables::of($roomManager->where('type', RoomEntity::TYPE_PUBLIC_GROUP)->get())
            ->editColumn('edit', function($chat_intent) {
                return route('admin.chat-public-group.edit', ['id' => $chat_intent->id]);
            })
            ->editColumn('delete', function($chat_intent) {
                return route('admin.chat-public-group.delete', ['id' => $chat_intent->id]);
            })
            ->editColumn('avatar_url', function($chat_intent) use ($disk) {
                if($chat_intent->avatar)
                    return '<img src="'. $disk->url($chat_intent->avatar) .'" style="width: 65px" />';
                else
                    return null;
            })
            ->rawColumns(['avatar_url'])
            ->make(true);
    }

    public function index()
    {
        $chat_location = EntityManager::create(RoomEntity::class);
        return view('chat-public-group::index')->with('chat_location', $chat_location);
    }

    public function store(StoreRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.chat-public-group.index'))->withSuccess('Add new Room successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function edit(string $id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $chat_location = $roomManager->where('id', $id)->where('status', 'active')->first();
        return view('chat-public-group::index')->with('chat_location', $chat_location);
    }

    public function update(UpdateRequest $request, string $id)
    {
        if ($request->validated()) {
            $request->save($id);
            return redirect(route('admin.chat-public-group.index'))->withSuccess('Update Room successful!');
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
