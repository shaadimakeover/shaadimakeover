<?php

namespace App\Http\Livewire\Admin\Post;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Http\Livewire\Traits\AlertMessage;
use App\Models\MakeupArtistPost;

class PostListing extends Component
{
    use WithPagination;
    use WithSorting;
    use AlertMessage;
    public $perPageList = [], $bulkDelIds = [], $selectAll = false;
    public $badgeColors = ['info', 'success', 'brand', 'dark', 'primary', 'warning'];
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirm', 'changeStatus', 'deleteSelected'];
    public $searchUser, $searchTitle, $searchDesc, $searchStatus = -1, $perPage = 5;


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
        $this->searchUser = "";
        $this->searchTitle = "";
        $this->searchDesc = "";
        $this->searchStatus = -1;
    }
    public function render()
    {

        $postQuery = MakeupArtistPost::query();

        if ($this->searchUser) {
            $postQuery->whereHas('artist', function ($q) {
                $q->WhereRaw("concat(first_name,' ', last_name) like '%" . trim($this->searchUser) . "%' ");
            });
        }

        if ($this->searchTitle) {
            $postQuery->where('post_title', 'like', '%' . trim($this->searchTitle) . '%');
        }

        if ($this->searchDesc)
            $postQuery->where('post_desc', 'like', '%' . trim($this->searchDesc) . '%');

        if ($this->searchStatus >= 0)
            $postQuery->where('status', $this->searchStatus);

        return view('livewire.admin.post.post-listing', [
            'posts' => $postQuery
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function deleteAttempt($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "You won't be able to recover this post!", 'Yes, delete!', 'deleteConfirm', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }
    public function deleteConfirm($id)
    {
        MakeupArtistPost::destroy($id);
        $this->showModal('success', 'Success', 'Post has been deleted successfully');
    }

    public function changeStatusConfirm($id)
    {
        $this->showConfirmation("warning", 'Are you sure?', "Do you want to change this status?", 'Yes, Change!', 'changeStatus', ['id' => $id]); //($type,$title,$text,$confirmText,$method)
    }

    public function changeStatus(MakeupArtistPost $post)
    {
        $post->fill(['status' => ($post->status == 1) ? 0 : 1])->save();

        $this->showModal('success', 'Success', 'Post status has been changed successfully');
    }
}
