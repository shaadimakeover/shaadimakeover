<?php

namespace App\Http\Livewire\Admin\user;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\AlertMessage;

class UserList extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [], $bulkDelIds = [], $selectAll = false;
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    public $searchName, $searchEmail, $searchPhone, $searchStatus = -1, $perPage = 5;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];

    public function mount()
    {
        $this->perPageList = [
            ['value' => 5, 'text' => "5"],
            ['value' => 10, 'text' => "10"],
            ['value' => 20, 'text' => "20"],
            ['value' => 50, 'text' => "50"],
            ['value' => 100, 'text' => "100"]
        ];

        $this->searchStatus = (request('status') != null) ? (request('status') == 'active' ? 1 : 0) : -1;
    }
    public function getRandomColor()
    {
        $arrIndex = array_rand($this->badgeColors);
        return $this->badgeColors[$arrIndex];
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function search()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->searchName = "";
        $this->searchEmail = "";
        $this->searchPhone = "";
        $this->searchStatus = -1;
    }

    public function render()
    {
        $userQuery = User::query();
        if ($this->searchName)
            $userQuery->WhereRaw(
                "concat(first_name,' ', last_name) like '%" . trim($this->searchName) . "%' "
            );
        if ($this->searchEmail)
            $userQuery->where('email', 'like', '%' . trim($this->searchEmail) . '%');
        if ($this->searchPhone)
            $userQuery->where('phone', 'like', '%' . trim($this->searchPhone) . '%');
        if ($this->searchStatus >= 0)
            $userQuery->where('active', $this->searchStatus);

        return view('livewire.admin.user.user-list', [
            'users' => $userQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->role('USER')
                ->paginate($this->perPage)
        ]);
    }

    public function deleteConfirm($id)
    {
        User::destroy($id);
        $this->showModal('success', 'Success', 'User has been deleted successfully');
    }
    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this user!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function updatedSelectAll($value)
    {
        if ($value) {

            $this->bulkDelIds = User::role('USER')->pluck('id');
        } else {
            $this->bulkDelIds = [];
        }
    }

    public function selectVal()
    {
        $this->selectAll = false;
    }

    public function bulkDeleteAttempt()
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this data !", 'Yes, delete!', 'deleteSelected', []); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteSelected()
    {
        User::query()->whereIn('id', $this->bulkDelIds)->delete();
        $this->bulkDelIds = [];
        $this->selectAll = false;
        $this->showModal('success', 'Success', 'Users have been deleted successfully');
    }

    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function changeStatus(User $user)
    {
        $user->fill(['active' => ($user->active == 1) ? 0 : 1])->save();
        if ($user->active != 1) {
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
        }
        $this->showModal('success', 'Success', 'User status has been changed successfully');
    }
}
