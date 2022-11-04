<?php

namespace App\Http\Livewire\Admin\Faq;

use App\Http\Livewire\Traits\AlertMessage;
use App\Models\Category;
use App\Models\Faq;
use Livewire\Component;
use Illuminate\Validation\Rule;

class FaqCreateEdit extends Component
{
    use AlertMessage;
    public $question,$answer, $active, $faq;
    public $isEdit = false;
    public $statusList = [];
    protected $listeners = ['refreshProducts' => '$refresh'];


    public function mount($faq = null)
    {
        if ($faq) {
            $this->faq = $faq;
            $this->fill($this->faq);
            $this->isEdit = true;
        } else
            $this->faq = new Faq();

        $this->statusList = [
            ['value' => 0, 'text' => "Choose Status"],
            ['value' => 1, 'text' => "Active"],
            ['value' => 0, 'text' => "Inactive"]
        ];
    }

    public function validationRuleForSave(): array
    {
        return
            [
                'question' => ['required', 'max:255'],
                'answer' => ['required'],
                'active' => ['required'],
            ];
    }
    public function validationRuleForUpdate(): array
    {
        return
            [
                'question' => ['required', 'max:255'],
                'answer' => ['required'],
                'active' => ['required'],
            ];
    }

    public function saveOrUpdate()
    {
        $this->faq->fill($this->validate($this->isEdit ? $this->validationRuleForUpdate() : $this->validationRuleForSave()))->save();
        $msgAction = 'Faq has been ' . ($this->isEdit ? 'updated' : 'added') . ' successfully';
        $this->showToastr("success", $msgAction);

        return redirect()->route('faq.index');
    }
    
    public function render()
    {
        return view('livewire.admin.faq.faq-create-edit');
    }
}
