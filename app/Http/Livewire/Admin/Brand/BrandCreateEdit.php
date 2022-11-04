<?php

namespace App\Http\Livewire\Admin\Brand;

use Livewire\Component;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Brand;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BrandCreateEdit extends Component
{
    use AlertMessage;
    use WithFileUploads;
    public $brand, $isEdit = false, $blankArr = [], $statusList = [], $categoryList = [];
    protected $listeners = ['refreshProducts' => '$refresh'];
    public $title, $short_description, $long_description, $meta_key, $meta_description, $thumbnail, $active;
    public $model_image, $imgId, $model_documents;

    public function mount($brand = null)
    {
        if ($brand) {
            $this->brand = $brand;
            $this->fill($this->brand);
            $this->isEdit = true;
        } else {
            $this->brand = new Brand();
        }

        $this->model_image = Media::where(['model_id' => $this->brand->id, 'collection_name' => 'brand'])->first();
        if (!$this->model_image == null) {
            $this->imgId = $this->model_image->id;
        }

        $this->blankArr = [
            "value" => 0, "text" => "== Select One =="
        ];

        $this->statusList = [
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];
    }

    public function validationRuleForSave(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['required', 'max:255'],
            'meta_key' => ['required', 'max:255'],
            'meta_description' => ['required', 'max:255'],
            'thumbnail' => ['nullable', 'max:255'],
            'active' => ['required']
        ];
    }
    public function validationRuleForUpdate(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['required', 'max:255'],
            'meta_key' => ['required', 'max:255'],
            'meta_description' => ['required', 'max:255'],
            'thumbnail' => ['nullable', 'max:255'],
            'active' => ['required']
        ];
    }

    public function saveOrUpdate()
    {
        $this->brand->fill($this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave()))->save();
        if ($this->thumbnail) {
            if ($this->imgId) {
                // delete previous image in the database
                $item = Media::find($this->imgId);
                $item->delete();
                //Insert new image in the database
                $this->brand->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->thumbnail->getClientOriginalName())
                    ->toMediaCollection('brand');

                $this->brand->update(['thumbnail' => $this->thumbnail->getClientOriginalName()]);
            } else {
                $this->brand->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->thumbnail->getClientOriginalName())
                    ->toMediaCollection('brand');
                $this->brand->update(['thumbnail' => $this->thumbnail->getClientOriginalName()]);
            }
        }

        $msgAction = 'Brand has been ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('brand.index');
    }
    public function render()
    {
        return view('livewire.admin.brand.brand-create-edit');
    }
}
