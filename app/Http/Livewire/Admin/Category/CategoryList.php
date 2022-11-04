<?php

namespace App\Http\Livewire\Admin\Category;

use App\Helpers\CategoryHelper;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryList extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [], $perPage = 5, $bulkDelIds = [], $selectAll = false, $categoryList = [];
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];

    public $searchName, $searchStatus = -1;

    public function mount()
    {
        $this->perPageList = [
            ['value' => 5, 'text' => "5"],
            ['value' => 10, 'text' => "10"],
            ['value' => 20, 'text' => "20"],
            ['value' => 50, 'text' => "50"],
            ['value' => 100, 'text' => "100"]
        ];

        $this->categoryList = CategoryHelper::getCategoryTree();
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
        $categories =  CategoryHelper::getCategoryTree();
        $data = $this->paginate($categories, $this->perPage);
        return view('livewire.admin.category.category-list', ['categories' => $data]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->bulkDelIds = Category::pluck('id');
        } else {
            $this->bulkDelIds = [];
        }
    }

    public function selectVal()
    {
        $this->selectAll = false;
    }

    //
    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this category!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteConfirm($id)
    {
        Category::destroy($id);
        $this->showModal('success', 'Success', 'Category has been deleted successfully');
    }

    //
    public function bulkDeleteAttempt()
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this data !", 'Yes, delete!', 'deleteSelected', []); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteSelected()
    {
        Category::query()->whereIn('id', $this->bulkDelIds)->delete();
        $this->bulkDelIds = [];
        $this->selectAll = false;
        $this->showModal('success', 'Success', 'Categories have been deleted successfully');
    }

    //
    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
    public function changeStatus(Category $category)
    {
        $category->update(['status' => ($category->status == 1) ? 0 : 1]);
        $this->showModal('success', 'Success', 'Category status has been changed successfully');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function paginate($items, $perPage, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
