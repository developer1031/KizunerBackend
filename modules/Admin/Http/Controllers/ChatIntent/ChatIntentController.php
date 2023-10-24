<?php

namespace Modules\Admin\Http\Controllers\ChatIntent;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Http\Requests\ChatIntent\StoreRequest;
use Modules\Admin\Http\Requests\ChatIntent\UpdateRequest;
use Modules\Admin\Http\Requests\Package\UpdateRequest as PackageUpdateRequest;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Kizuner\Models\Status;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;
use Yajra\DataTables\DataTables;

class ChatIntentController
{
    public function data()
    {
        return DataTables::of(ChatIntent::all())
            ->editColumn('edit', function($chat_intent) {
                return route('admin.chat-intent.edit', ['id' => $chat_intent->id]);
            })
            ->editColumn('delete', function($chat_intent) {
                return route('admin.chat-intent.delete', ['id' => $chat_intent->id]);
            })
            ->make(true);
    }

    public function index()
    {
        $chat_intent = new ChatIntent();
        return view('chat-intent::index')->with('chat_intent', $chat_intent);
    }

    public function store(StoreRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.chat-intent.index'))->withSuccess('Add new Intent successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function edit(string $id)
    {
        $chat_intent = ChatIntent::find($id);
        return view('chat-intent::index')->with('chat_intent', $chat_intent);
    }

    public function update(UpdateRequest $request)
    {
        if ($request->validated()) {
            $request->save();
            return redirect(route('admin.chat-intent.index'))->withSuccess('Update Intent successful!');
        }
        return redirect()->back()->withError('Your data is invalid!');
    }

    public function destroy(string $id)
    {
        $chat_intent = ChatIntent::find($id);
        $chat_intent->delete();
        return redirect()->back()->withSuccess('Delete successful!');
    }

}
