<?php

namespace App\Http\Livewire\Admin\Brand;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Brand;

class BrandList extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [], $bulkDelIds = [], $selectAll = false;
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];
    public $searchName, $searchStatus = -1, $perPage = 5;

    public function mount()
    {
        $this->perPageList = [
            ['value' => 5, 'text' => "5"],
            ['value' => 10, 'text' => "10"],
            ['value' => 20, 'text' => "20"],
            ['value' => 50, 'text' => "50"],
            ['value' => 100, 'text' => "100"]
        ];
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
        $this->searchStatus = -1;
    }

    public function render()
    {
        $brandQuery = Brand::query();

        if ($this->searchName)
            $brandQuery->where('title', 'like', '%' . trim($this->searchName) . '%');

        if ($this->searchStatus >= 0)
            $brandQuery->where('active', $this->searchStatus);

        return view('livewire.admin.brand.brand-list', [
            'brands' => $brandQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->bulkDelIds = Brand::pluck('id');
        } else {
            $this->bulkDelIds = [];
        }
    }

    public function selectVal()
    {
        $this->selectAll = false;
    }


    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this brand!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteConfirm($id)
    {
        Brand::destroy($id);
        $this->showModal('success', 'Success', 'Brand has been deleted successfully');
    }

    public function bulkDeleteAttempt()
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this data !", 'Yes, delete!', 'deleteSelected', []); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteSelected()
    {
        Brand::query()->whereIn('id', $this->bulkDelIds)->delete();
        $this->bulkDelIds = [];
        $this->selectAll = false;
        $this->showModal('success', 'Success', 'Brands have been deleted successfully');
    }

    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function changeStatus(Brand $user)
    {
        $user->fill(['active' => ($user->active == 1) ? 0 : 1])->save();
        if ($user->active != 1) {
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
        }
        $this->showModal('success', 'Success', 'Brand status has been changed successfully');
    }
}
