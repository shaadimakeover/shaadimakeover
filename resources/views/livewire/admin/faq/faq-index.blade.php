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

            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 20%;"
                aria-label="Question: activate to sort column ascending">Question </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 55%;"
                aria-label="Answer: activate to sort column ascending">Answer </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 10%;"
                aria-label="Answer: activate to sort column ascending">Active </th>
            <th class="align-center" rowspan="1" colspan="1" style="width: 15%;" aria-label="Actions">Actions</th>

        </tr>

        <tr class="filter">
            <th>
                <x-admin.input type="search" wire:model.defer="searchQuestion" placeholder="" autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
                <x-admin.input type="search" wire:model.defer="searchAnswer" placeholder="" autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
                <select class="form-control form-control-sm form-filter kt-input" wire:model.defer="searchStatus"
                    title="Select" data-col-index="2">
                    <option value="">Select One</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </th>

            <th>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-brand kt-btn btn-sm kt-btn--icon" wire:click="search">
                            <span>
                                <i class="la la-search"></i>
                                <span>Search</span>
                            </span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary kt-btn btn-sm kt-btn--icon" wire:click="resetSearch">
                            <span>
                                <i class="la la-close"></i>
                                <span>Reset</span>
                            </span>
                        </button>
                    </div>
                </div>
            </th>
        </tr>
    </x-slot>

    <x-slot name="tbody">
        @forelse($faqs as $faq)
            <tr role="row" class="odd">

                <td>{{ $faq->question }}</td>
                <td>{{ $faq->answer }}</td>
                <td class="align-center"><span
                    class="kt-badge  kt-badge--{{ $faq->active == 1 ? 'success' : 'warning' }} kt-badge--inline kt-badge--pill cursor-pointer"
                    wire:click="changeStatusConfirm({{ $faq->id }})">{{ $faq->active == 1 ? 'Active' : 'Inactive' }}</span>
            </td>
                

                <x-admin.td-action>
                    <a class="dropdown-item" href="{{ route('faq.edit', $faq->id) }}"><i class="la la-edit"></i>
                        Edit</a>

                    <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteAttempt({{ $faq->id }})"
                        class="success p-0" title="Delete">
                        <i class="flaticon-delete font-large-3 mr-2"></i>Delete
                    </a>

                </x-admin.td-action>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="align-center">No records available</td>
            </tr>
        @endforelse

    </x-slot>
    <x-slot name="pagination">
        {{ $faqs->links() }}
    </x-slot>
    <x-slot name="showingEntries">
        Showing {{ $faqs->firstitem() ?? 0 }} to {{ $faqs->lastitem() ?? 0 }} of {{ $faqs->total() }} entries
    </x-slot>
</x-admin.table>
