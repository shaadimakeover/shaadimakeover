<?php

namespace App\Http\Livewire\Admin\Vendor;

use Livewire\Component;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VendorCreateEdit extends Component
{
    use WithFileUploads;
    use AlertMessage;
    public $first_name, $last_name, $email, $password, $phone, $active, $password_confirmation, $vendor, $model_id, $address;
    public $isEdit = false, $statusList = [], $photo, $photos = [];
    public $model_image, $imgId, $model_documents;

    protected $listeners = ['refreshProducts' => '$refresh'];

    public function mount($vendor = null)
    {
        if ($vendor) {
            $this->vendor = $vendor;
            $this->fill($this->vendor);
            $this->isEdit = true;
        } else {
            $this->vendor = new User;
        }

        $this->blankArr = [
            "value" => 0, "text" => "== Select One =="
        ];
        $this->statusList = [
            ['value' => 0, 'text' => "Choose Status"],
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];
        $this->model_image = Media::where(['model_id' => $this->vendor->id, 'collection_name' => 'images'])->first();
        if (!$this->model_image == null) {
            $this->imgId = $this->model_image->id;
        }
        $this->model_documents = Media::where(['model_id' => $this->vendor->id, 'collection_name' => 'documents'])->get();
    }

    public function validationRuleForSave(): array
    {
        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['nullable', 'max:255'],
            'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:255', Rule::unique('users')],
            'phone' => ['required', Rule::unique('users'), 'digits_between:8,15', 'numeric'],
            'password' => ['required', 'max:255', 'min:6'],
            'password_confirmation' => ['required', 'max:255', 'min:6', 'same:password'],
            'active' => ['required'],
            'photo' => ['required'],
            'address' => ['nullable']
        ];
    }
    public function validationRuleForUpdate(): array
    {
        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['nullable', 'max:255'],
            'active' => ['required'],
            'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:255', Rule::unique('users')->ignore($this->vendor->id)],
            'phone' => ['required', Rule::unique('users')->ignore($this->vendor->id), 'digits_between:8,15', 'numeric'],
            'address' => ['nullable']
        ];
    }

    public function saveOrUpdate()
    {
        $this->vendor->fill($this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave()))->save();
        if ($this->photo) {
            if ($this->imgId) {
                $item = Media::find($this->imgId);
                $item->delete(); // delete previous image in the database

                $this->vendor->addMedia($this->photo->getRealPath())
                    ->usingName($this->photo->getClientOriginalName())
                    ->toMediaCollection('images');
            } else {
                $this->vendor->addMedia($this->photo->getRealPath())
                    ->usingName($this->photo->getClientOriginalName())
                    ->toMediaCollection('images');
            }
        }
        if ($this->photos) {
            foreach ($this->photos as $photo) {
                $this->vendor->addMedia($photo->getRealPath())
                    ->usingName($photo->getClientOriginalName())
                    ->toMediaCollection('documents');
            }
        }
        if (!$this->isEdit) {
            $this->vendor->assignRole('VENDOR');
        }
        $msgAction = 'Vendor was ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('vendors.index');
    }

    public function deleteDocuments($id)
    {
        $item = Media::find($id);
        $item->delete(); // delete previous image in the database
        $this->showModal('success', 'Success', 'Document deleted successfully');
        $this->emit('refreshComponents');
    }
    public function render()
    {
        return view('livewire.admin.vendor.vendor-create-edit');
    }
}
