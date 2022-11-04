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
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 15%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Title
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 15%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Thumbnail
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 15%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Short Description
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 20%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Long Description
            </th>
            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 15%;" aria-label="Status: activate to sort column ascending">Status</th>
            <th class="align-center" rowspan="1" colspan="1" style="width: 20%;" aria-label="Actions">Actions</th>
        </tr>

        <tr class="filter">
            <th>
                <x-admin.input type="search" wire:model.defer="searchName" placeholder="" autocomplete="off"
                    class="form-control-sm form-filter" />
            </th>
            <th>
                <x-admin.input type="search" disabled autocomplete="off" class="form-control-sm form-filter" />
            </th>
            <th>
                <x-admin.input type="search" disabled autocomplete="off" class="form-control-sm form-filter" />
            </th>
            <th>
                <x-admin.input type="search" disabled autocomplete="off" class="form-control-sm form-filter" />
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
        @forelse($brands as $brand)
            @php
                $model_image = Spatie\MediaLibrary\MediaCollections\Models\Media::where(['model_id' => $brand->id, 'collection_name' => 'brand'])->first();
            @endphp
            <tr role="row" class="odd">
                <td class="align-center">{{ $brand->title }}</td>
                <td class="align-center">
                    @if ($brand->thumbnail)
                        <img src="{{ $model_image->getUrl() }}" alt="" srcset=""
                            style="width: 50px; height:50px;">
                    @else
                        <img src="{{ asset('admin_assets/logo/favicon.ico') }}" alt="" srcset="">
                    @endif
                </td>
                <td class="align-center">{{ $brand->short_description }}</td>
                <td class="align-center">{!! $brand->long_description !!}</td>
                <td class="align-center"><span
                        class="kt-badge  kt-badge--{{ $brand->active == 1 ? 'success' : 'warning' }} kt-badge--inline kt-badge--pill cursor-pointer"
                        wire:click="changeStatusConfirm({{ $brand->id }})">{{ $brand->active == 1 ? 'Active' : 'Inactive' }}</span>
                </td>
                <x-admin.td-action>
                    <a class="dropdown-item" href="{{ route('brand.edit', ['brand' => $brand->id]) }}"><i
                            class="la la-edit"></i> Edit</a>
                    <button href="#" class="dropdown-item" wire:click="deleteAttempt({{ $brand->id }})"><i
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
        {{ $brands->links() }}
    </x-slot>
    <x-slot name="showingEntries">
        Showing {{ $brands->firstitem() ?? 0 }} to {{ $brands->lastitem() ?? 0 }} of {{ $brands->total() }} entries
    </x-slot>
</x-admin.table>
