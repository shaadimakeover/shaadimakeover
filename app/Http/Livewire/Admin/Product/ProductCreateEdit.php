<?php

namespace App\Http\Livewire\Admin\Product;

use App\Helpers\CategoryHelper;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductCreateEdit extends Component
{
    use AlertMessage;
    use WithFileUploads;
    public $isEdit = false, $blankArr = [], $statusList = [], $categoryList = [], $brandList = [], $product;
    protected $listeners = ['refreshProducts' => '$refresh'];
    public $title, $category_id, $brand_id, $mrp_price, $sell_price, $short_description, $long_description, $meta_key, $meta_description, $thumbnail, $thumbnails, $active;
    public $model_image, $imgId, $model_documents;

    public function mount($product = null)
    {
        if ($product) {
            $this->product = $product;
            $this->fill($this->product);
            $this->isEdit = true;
        } else {
            $this->product = new Product();
        }
        $this->blankArr = [
            "value" => 0, "text" => "== Select One =="
        ];

        $this->statusList = [
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];

        $this->categoryList = CategoryHelper::getCategoryTree();
        $this->brandList = Brand::where('active', 1)->get()->toArray();

        $this->model_image = Media::where(['model_id' => $this->product->id, 'collection_name' => 'products'])->first();
        if (!$this->model_image == null) {
            $this->imgId = $this->model_image->id;
        }
        $this->model_documents = Media::where(['model_id' => $this->product->id, 'collection_name' => 'products'])->get();
    }

    public function validationRuleForSave(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'category_id' => ['required', 'max:255'],
            'brand_id' => ['required', 'max:255'],
            'mrp_price' => ['required', 'max:255'],
            'sell_price' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['required', 'max:255'],
            'meta_key' => ['required', 'max:255'],
            'meta_description' => ['required', 'max:255'],
            'active' => ['required']
        ];
    }
    public function validationRuleForUpdate(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'category_id' => ['required', 'max:255'],
            'brand_id' => ['required', 'max:255'],
            'mrp_price' => ['required', 'max:255'],
            'sell_price' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['required', 'max:255'],
            'meta_key' => ['required', 'max:255'],
            'meta_description' => ['required', 'max:255'],
            'active' => ['required']
        ];
    }

    public function saveOrUpdate()
    {
        $input = $this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave());
        $input['user_id'] = Auth::id();
        $this->product->fill($input)->save();
        if ($this->thumbnail) {
            if ($this->imgId) {
                // delete previous image in the database
                $item = Media::find($this->imgId);
                $item->delete();

                // Insert new image in the database
                $this->product->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->photo->getClientOriginalName())
                    ->toMediaCollection('products');
            } else {
                $this->product->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->thumbnail->getClientOriginalName())
                    ->toMediaCollection('products');
            }
        }
        if ($this->thumbnails) {
            foreach ($this->thumbnails as $thumbnail) {
                $this->product->addMedia($thumbnail->getRealPath())
                    ->usingName($thumbnail->getClientOriginalName())
                    ->toMediaCollection('products');
            }
        }

        $msgAction = 'Product has been ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('product.index');
    }
    public function render()
    {
        return view('livewire.admin.product.product-create-edit');
    }
}
