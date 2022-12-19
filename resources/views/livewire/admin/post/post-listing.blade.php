<x-admin.table>
    <x-slot name="search">
    </x-slot>
    <x-slot name="perPage">
        <label>Show
            <x-admin.dropdown wire:model="perPage" class="custom-select custom-select-sm form-control form-control-sm">
                @foreach ($perPageList as $page)
                    <x-admin.dropdown-item :value="$page['value']" :text="$page['text']" />
                @endforeach
            </x-admin.dropdown> entries
        </label>
    </x-slot>

    <x-slot name="thead">
        <tr role="row">
            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 15%;" aria-sort="ascending" aria-label="Agent: activate to sort column descending">Artist
                Name
            </th>

            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 15%;" aria-sort="ascending" aria-label="Agent: activate to sort column descending">Title
            </th>

            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 30%;" aria-sort="ascending" aria-label="Agent: activate to sort column descending">
                Description
            </th>

            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 20%;" aria-sort="ascending" aria-label="Agent: activate to sort column descending">
                Attachment
            </th>

            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 10%;" aria-label="Status: activate to sort column ascending">Status</th>
            <th class="align-center" rowspan="1" colspan="1" style="width: 10%;" aria-label="Actions">Actions</th>
        </tr>

        <tr class="filter">
            <th>
                <x-admin.input type="search" wire:model.defer="searchUser" placeholder="" autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>

            <th>
                <x-admin.input type="search" wire:model.defer='searchTitle' autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
                <x-admin.input type="search" wire:model.defer='searchDesc' autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
            </th>


            <th>
                <select class="form-control form-control-sm form-filter kt-input" wire:model.defer="searchStatus"
                    title="Select" data-col-index="2">
                    <option value="-1">Select One</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </th>
            <th>
                <div class="row justify-content-center align-items-center">
                    <button class="btn btn-brand kt-btn btn-sm kt-btn--icon button-fx" wire:click="search">
                        <span>
                            <i class="la la-search"></i>
                            <span>Search</span>
                        </span>
                    </button>
                    <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon button-fx" wire:click="resetSearch">
                        <span>
                            <i class="la la-close"></i>
                            <span>Reset</span>
                        </span>
                    </button>
                </div>
            </th>
        </tr>
    </x-slot>

    <x-slot name="tbody">
        @forelse($posts as $post)
            <tr role="row" class="odd">
                <td class="align-center">{{ $post->artist->full_name ?? 'NULL' }}</td>
                <td class="align-center">{{ $post->post_title }}</td>
                <td class="align-center">{{ $post->post_desc }}</td>
                <td class="align-center">
                    @if ($post->post_attachment)
                        <img src="{{ asset($post->post_attachment) }}" alt="" srcset=""
                            style="width: 50px; height:50px;">
                    @endif
                </td>
                <td class="align-center"><span
                        class="kt-badge  kt-badge--{{ $post->status == 1 ? 'success' : 'warning' }} kt-badge--inline kt-badge--pill cursor-pointer"
                        wire:click="changeStatusConfirm({{ $post->id }})">{{ $post->status == 1 ? 'Active' : 'Inactive' }}</span>
                </td>
                <x-admin.td-action>
                    {{-- <a class="dropdown-item" href="{{ route('posts.edit', ['post' => $post->id]) }}"><i
                            class="la la-edit"></i> Edit</a> --}}
                    <button href="#" class="dropdown-item" wire:click="deleteAttempt({{ $post->id }})"><i
                            class="fa fa-trash"></i> Delete</button>
                </x-admin.td-action>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="align-center">No records available</td>
            </tr>
        @endforelse
    </x-slot>
    <x-slot name="pagination">
        {{ $posts->links() }}
    </x-slot>
    <x-slot name="showingEntries">
        Showing {{ $posts->firstitem() ?? 0 }} to {{ $posts->lastitem() ?? 0 }} of {{ $posts->total() }}
        entries
    </x-slot>
</x-admin.table>
