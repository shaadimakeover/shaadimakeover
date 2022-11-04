<?php

namespace App\Http\Livewire\Admin\Faq;

use App\Http\Livewire\Traits\AlertMessage;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Faq;

class FaqIndex extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [];
    protected $paginationTheme = 'bootstrap';
    public $searchQuestion, $searchAnswer, $perPage = 5, $searchStatus = -1;

    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];

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
        $this->searchQuestion = "";
        $this->searchAnswer = "";
        $this->searchStatus = -1;
    }

    public function render()
    {
        $faqQuery = Faq::query();
        if ($this->searchQuestion)
            $faqQuery->orWhere('question', 'like', '%' . trim($this->searchQuestion) . '%');
        if ($this->searchAnswer)
            $faqQuery->orWhere('answer', 'like', '%' . trim($this->searchAnswer) . '%');
        if ($this->searchStatus >= 0)
            $faqQuery->orWhere('active', 'like', '%' . trim($this->searchStatus) . '%');

        return view('livewire.admin.faq.faq-index', [
            'faqs' => $faqQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }


    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function changeStatus(Faq $faq)
    {
        $faq->update(['active' => ($faq->active == 1) ? 0 : 1]);
        $this->showModal('success', 'Success', 'Faq status has been changed successfully');
    }

    public function deleteConfirm($id)
    {
        Faq::destroy($id);
        $this->showModal('success', 'Success', 'Faq has been deleted successfully');
    }
    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this faq!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
}
