<?php

namespace App\Http\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\AlertMessage;

class ProductList extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [], $bulkDelIds = [], $selectAll = false;
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];

    public $searchName, $searchCategory, $searchBrand, $searchMrpPrice, $searchSellPrice, $searchStatus = -1, $perPage = 5;

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
        $this->searchCategory = "";
        $this->searchBrand = "";
        $this->searchMrpPrice = "";
        $this->searchSellPrice = "";
        $this->searchStatus = -1;
    }

    public function render()
    {
        $productQuery = Product::query();

        if ($this->searchName)
            $productQuery->where('title', 'like', '%' . trim($this->searchName) . '%');

        if ($this->searchCategory) {
            $productQuery->whereHas('category', function ($q) {
                $q->WhereRaw("title like '%" . trim($this->searchCategory) . "%' ");
            });
        }

        if ($this->searchBrand) {
            $productQuery->whereHas('brand', function ($q) {
                $q->WhereRaw("title like '%" . trim($this->searchBrand) . "%' ");
            });
        }

        if ($this->searchMrpPrice)
            $productQuery->where('mrp_price', 'like', '%' . trim($this->searchMrpPrice) . '%');

        if ($this->searchSellPrice)
            $productQuery->where('sell_price', 'like', '%' . trim($this->searchSellPrice) . '%');

        if ($this->searchStatus >= 0)
            $productQuery->where('active', $this->searchStatus);

        return view('livewire.admin.product.product-list', [
            'products' => $productQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->bulkDelIds = Product::pluck('id');
        } else {
            $this->bulkDelIds = [];
        }
    }

    public function selectVal()
    {
        $this->selectAll = false;
    }

    public function deleteConfirm($id)
    {
        Product::destroy($id);
        $this->showModal('success', 'Success', 'Product has been deleted successfully');
    }
    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this Product!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function bulkDeleteAttempt()
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this data !", 'Yes, delete!', 'deleteSelected', []); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteSelected()
    {
        Product::query()->whereIn('id', $this->bulkDelIds)->delete();
        $this->bulkDelIds = [];
        $this->selectAll = false;
        $this->showModal('success', 'Success', 'Product have been deleted successfully');
    }

    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function changeStatus(Product $product)
    {
        $product->fill(['active' => ($product->active == 1) ? 0 : 1])->save();
        if ($product->active != 1) {
            $product->tokens->each(function ($token, $key) {
                $token->delete();
            });
        }
        $this->showModal('success', 'Success', 'Product status has been changed successfully');
    }
}
