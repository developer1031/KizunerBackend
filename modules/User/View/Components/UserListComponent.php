<?php

namespace Modules\User\View\Components;

use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserListComponent extends Component
{

    use WithPagination;

    public $component = 'user::components.user-list';

    public $search = '';
    public $page = 1;

    protected $updatesQueryString = [
        'foo',
        ['search' => ['except' => '']],
        ['page' => ['except' => 1]],
    ];

    public function mount()
    {
        $this->search = request()->query('search', $this->search);
    }

    public function render()
    {
        return view(
            $this->component, [
                'users' => User::where('name', 'like', '%'.$this->search.'%')
                                    ->orWhere('email', 'like', '%'.$this->search.'%')
                                    ->orWhere('age', 'like', '%'.$this->search.'%')
                                    ->paginate(5)
            ]);
    }


}
