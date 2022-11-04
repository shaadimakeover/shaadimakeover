<x-admin.table>
    {{-- <x-slot name="search">
        <x-admin.input type="search" class="form-control form-control-sm" wire:model.debounce.500ms="search"
            aria-controls="kt_table_1" id="generalSearch" />
    </x-slot> --}}
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
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 80%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Title
            </th>
            <th class="align-center" rowspan="1" colspan="1" style="width: 20%;" aria-label="Actions">Actions</th>
        </tr>

        <tr class="filter">
            <th>
                <x-admin.input type="search" wire:model.defer="searchTitle" placeholder="" autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
                <div class="row justify-content-center align-items-center">
                    <button class="btn btn-brand kt-btn btn-sm kt-btn--icon" wire:click="search">
                        <span>
                            <i class="la la-search"></i>
                            <span>Search</span>
                        </span>
                    </button>
                    <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon" wire:click="resetSearch">
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
        @forelse($cms as $cms_details)
            <tr role="row" class="odd">
                <td class="sorting_1" tabindex="0">
                    <div class="kt-user-card-v2">
                        <div class="kt-user-card-v2__details">
                            <span class="kt-user-card-v2__name">{{ $cms_details->title }}</span>
                        </div>
                    </div>
                </td>
                <x-admin.td-action>
                    <a class="dropdown-item" href="{{ route('cms.edit', [$cms_details->id]) }}"><i
                            class="la la-edit"></i> Edit</a>

                    {{-- <button href="#" class="dropdown-item" wire:click="deleteAttempt({{ $cms_details->id }})"><i
                            class="fa fa-trash"></i> Delete</button> --}}
                </x-admin.td-action>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="align-center">No records available</td>
            </tr>
        @endforelse
    </x-slot>
    <x-slot name="pagination">
        {{ $cms->links() }}
    </x-slot>
    <x-slot name="showingEntries">
        Showing {{ $cms->firstitem() }} to {{ $cms->lastitem() }} of {{ $cms->total() }} entries
    </x-slot>
</x-admin.table>
