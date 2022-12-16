<?php

namespace App\Http\Livewire\Admin\Banner;

use App\Helpers\ImageHelper;
use Livewire\Component;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\User;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CreateEdit extends Component
{

    use AlertMessage;
    use WithFileUploads;
    public $banner, $isEdit = false, $blankArr = [], $statusList = [], $artistList = [];
    protected $listeners = ['refreshProducts' => '$refresh'];
    public $artist_id, $photo, $title, $menu_order, $status, $imgId;
    public function mount($banner = null)
    {
        if ($banner) {
            $this->banner = $banner;
            $this->fill($this->banner);
            $this->isEdit = true;
            $this->imgId = $this->banner->thumbnail;
        } else {
            $this->banner = new Banner();
        }

        $this->blankArr = [
            "value" => 0, "text" => "== Select One =="
        ];

        $this->statusList = [
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];
        $this->artistList = User::Role('ARTIST')->select('id', 'first_name', 'last_name')->get()->toArray();
    }

    public function validationRuleForSave(): array
    {
        return [
            'artist_id' => ['required'],
            'photo' => ['required'],
            'title' => ['required', 'max:255'],
            'menu_order' => ['required', 'max:255'],
            'status' => ['required', 'max:255']
        ];
    }
    public function validationRuleForUpdate(): array
    {
        return [
            'artist_id' => ['required'],
            'photo' => ['nullable'],
            'title' => ['required', 'max:255'],
            'menu_order' => ['required', 'max:255'],
            'status' => ['required', 'max:255']
        ];
    }


    public function saveOrUpdate()
    {
        $dataVal = $this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave());
        $final_image_url = '';
        if ($this->photo) {
            if ($this->imgId) {
                unlink($this->imgId);
            }
            $file = $this->photo;
            $path = 'banner';
            $final_image_url = ImageHelper::customSaveImage($file, $path);
            $dataVal['thumbnail'] = $final_image_url;
        } else {
            $dataVal['thumbnail'] = $this->imgId;
        }

        $this->banner->fill($dataVal)->save();

        $msgAction = 'Banner has been ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);
        return redirect()->route('banners.index');
    }

    public function render()
    {
        return view('livewire.admin.banner.create-edit');
    }
}
