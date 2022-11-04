<?php

namespace App\Http\Livewire\Admin\Cms;

use App\Http\Livewire\Traits\AlertMessage;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Cms;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class CmsList extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [];
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    protected $listeners = ['deleteConfirm', 'changeStatus'];

    protected $paginationTheme = 'bootstrap';

    public $searchTitle, $perPage = 5;

    public function mount()
    {
        $this->perPageList = [
            ['value' => 5, 'text' => "5"],
            ['value' => 10, 'text' => "10"],
            ['value' => 20, 'text' => "20"],
            ['value' => 50, 'text' => "50"],
            ['value' => 100, 'text' => "100"]
        ];
        $this->sortDirection = 'asc';
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
        $this->searchTitle = "";
    }

    public function render()
    {
        $cmsQuery = Cms::query();
        if ($this->searchTitle)
            $cmsQuery->orWhere('title', 'like', '%' . trim($this->searchTitle) . '%');
        return view('livewire.admin.cms.cms-list', [
            'cms' => $cmsQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function deleteConfirm($id)
    {
        // dd($id);
        Cms::destroy($id);
        $this->showModal('success', 'Success', 'CMS has been deleted successfully');
    }

    public function deleteAttempt($id)
    {
        //dd($id);
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this recommendation!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
}
