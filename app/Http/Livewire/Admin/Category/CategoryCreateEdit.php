<?php

namespace App\Http\Livewire\Admin\Category;

use App\Helpers\CategoryHelper;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CategoryCreateEdit extends Component
{
    use AlertMessage;
    use WithFileUploads;
    public $category, $isEdit = false, $blankArr = [], $statusList = [], $categoryList = [];
    protected $listeners = ['refreshProducts' => '$refresh'];
    public $title, $parent_id = 0, $short_description, $long_description, $meta_key, $meta_description, $thumbnail, $active;
    public $model_image, $imgId, $model_documents;

    public function mount($category = null)
    {
        if ($category) {
            $this->category = $category;
            $this->fill($this->category);
            $this->isEdit = true;
        } else {
            $this->category = new Category;
        }

        $this->model_image = Media::where(['model_id' => $this->category->id, 'collection_name' => 'category'])->first();
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

        $this->categoryList = CategoryHelper::getCategoryTree();
    }

    public function validationRuleForSave(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'parent_id' => ['nullable', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['nullable', 'max:255'],
            'meta_key' => ['nullable', 'max:255'],
            'meta_description' => ['nullable', 'max:255'],
            'thumbnail' => ['nullable', 'max:255'],
            'active' => ['required']
        ];
    }
    public function validationRuleForUpdate(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'parent_id' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'long_description' => ['nullable', 'max:255'],
            'meta_key' => ['nullable', 'max:255'],
            'meta_description' => ['nullable', 'max:255'],
            'thumbnail' => ['nullable', 'max:255'],
            'active' => ['required']
        ];
    }

    public function saveOrUpdate()
    {
        //$this->category->fill($this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave()))->save();
        $input = $this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave());
        if ($this->isEdit && $this->parent_id) {
            $input['parent_id'] = $this->category->id == $this->parent_id ? 0 : $this->parent_id;
        }
        $this->category->fill($input)->save();

        if ($this->thumbnail) {
            if ($this->imgId) {
                $item = Media::find($this->imgId);
                $item->delete(); // delete previous image in the database

                $this->category->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->thumbnail->getClientOriginalName())
                    ->toMediaCollection('category');

                $this->category->update(['thumbnail' => $this->thumbnail->getClientOriginalName()]);
            } else {
                $this->category->addMedia($this->thumbnail->getRealPath())
                    ->usingName($this->thumbnail->getClientOriginalName())
                    ->toMediaCollection('category');
                $this->category->update(['thumbnail' => $this->thumbnail->getClientOriginalName()]);
            }
        }

        $msgAction = 'Category has been ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('category.index');
    }
    public function render()
    {
        return view('livewire.admin.category.category-create-edit');
    }
}
