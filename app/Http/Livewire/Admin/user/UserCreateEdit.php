<?php

namespace App\Http\Livewire\Admin\user;

use App\Http\Livewire\Traits\AlertMessage;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserCreateEdit extends Component
{
    use WithFileUploads;
    use AlertMessage;
    public $first_name, $last_name, $email, $password, $phone, $active, $password_confirmation, $user, $model_id, $address;
    public $isEdit = false, $statusList = [], $photo, $photos = [];
    public $model_image, $imgId, $model_documents;

    protected $listeners = ['refreshComponents' => '$refresh'];

    public function mount($user = null)
    {
        if ($user) {
            $this->user = $user;
            $this->fill($this->user);
            $this->isEdit = true;
        } else {
            $this->user = new User;
        }

        $this->blankArr = [
            "value" => 0, "text" => "== Select One =="
        ];
        $this->statusList = [
            ['value' => 0, 'text' => "Choose Status"],
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];
        $this->model_image = Media::where(['model_id' => $this->user->id, 'collection_name' => 'images'])->first();
        if (!$this->model_image == null) {
            $this->imgId = $this->model_image->id;
        }
        $this->model_documents = Media::where(['model_id' => $this->user->id, 'collection_name' => 'documents'])->get();
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
            'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'phone' => ['required', Rule::unique('users')->ignore($this->user->id), 'digits_between:8,15', 'numeric'],
            'address' => ['nullable']
        ];
    }

    public function saveOrUpdate()
    {
        $this->user->fill($this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave()))->save();
        if ($this->photo) {
            if ($this->imgId) {
                // delete previous image in the database
                $item = Media::find($this->imgId);
                $item->delete();

                // Insert new image in the database
                $this->user->addMedia($this->photo->getRealPath())
                    ->usingName($this->photo->getClientOriginalName())
                    ->toMediaCollection('images');
            } else {
                $this->user->addMedia($this->photo->getRealPath())
                    ->usingName($this->photo->getClientOriginalName())
                    ->toMediaCollection('images');
            }
        }
        if ($this->photos) {
            foreach ($this->photos as $photo) {
                $this->user->addMedia($photo->getRealPath())
                    ->usingName($photo->getClientOriginalName())
                    ->toMediaCollection('documents');
            }
        }
        if (!$this->isEdit) {
            $this->user->assignRole('USER');
        }
        $msgAction = 'User was ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('users.index');
    }

    public function deleteDocuments($id)
    {
        // delete previous image in the database
        $item = Media::find($id);
        $item->delete();

        $this->showModal('success', 'Success', 'Document deleted successfully');
        $this->emit('refreshComponents');
    }
    public function render()
    {
        return view('livewire.admin.user.user-create-edit');
    }
}
