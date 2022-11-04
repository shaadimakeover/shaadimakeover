<x-admin.table>
    <x-slot name="search"></x-slot>
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
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Title
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 20%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Thumbnail
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 15%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Short Description
            </th>
            <th tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1" style="width: 25%;"
                aria-sort="ascending" aria-label="Agent: activate to sort column descending">Long Description
            </th>
            <th class="align-center" tabindex="0" aria-controls="kt_table_1" rowspan="1" colspan="1"
                style="width: 10%;" aria-label="Status: activate to sort column ascending">Status</th>
            <th class="align-center" rowspan="1" colspan="1" style="width: 10%;" aria-label="Actions">Actions</th>
        </tr>
    </x-slot>

    <x-slot name="tbody">
        @forelse($categories as $data)
            @php
                $model_image = Spatie\MediaLibrary\MediaCollections\Models\Media::where(['model_id' => $data['id'], 'collection_name' => 'category'])->first();
            @endphp
            <tr role="row" class="odd">
                <td>{{ $data['title'] }}</td>
                <td>
                    @if ($data['thumbnail'])
                        <img src="{{ $model_image->getUrl() }}" alt="" srcset=""
                            style="width: 50px; height:50px;">
                    @else
                        <img src="{{ asset('admin_assets/logo/favicon.ico') }}" alt="" srcset="">
                    @endif
                </td>
                <td>{{ $data['short_description'] }}</td>
                <td>{!! $data['long_description'] !!}</td>
                <td class="align-center"><span
                        class="kt-badge  kt-badge--{{ $data['active'] == 1 ? 'success' : 'warning' }} kt-badge--inline kt-badge--pill cursor-pointer"
                        wire:click="changeStatusConfirm({{ $data['id'] }})">{{ $data['active'] == 1 ? 'Active' : 'Inactive' }}</span>
                </td>
                <x-admin.td-action>
                    <a class="dropdown-item" href="{{ route('category.edit', ['category' => $data['id']]) }}"><i
                            class="la la-edit"></i> Edit</a>
                    <button href="#" class="dropdown-item" wire:click="deleteAttempt({{ $data['id'] }})"><i
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
        {{ $categories->links() }}
    </x-slot>
    <x-slot name="showingEntries">
        Showing {{ $categories->firstitem() ?? 0 }} to {{ $categories->lastitem() ?? 0 }} of
        {{ $categories->total() }}
        entries
    </x-slot>
</x-admin.table>
