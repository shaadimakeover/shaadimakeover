<x-admin.form-section submit="saveOrUpdate">
    <x-slot name="form">
        <x-admin.form-group>
            <x-admin.lable value="Artist" required />
            <x-admin.dropdown wire:model.defer="artist_id" placeHolderText="Please select one" autocomplete="off"
                class="{{ $errors->has('artist_id') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($artistList as $artist)
                    <x-admin.dropdown-item :value="$artist['id']" :text="$artist['full_name']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="artist_id" />
        </x-admin.form-group>

        <x-admin.form-group class="col-lg-6 col-md-6">
            <x-admin.lable value="Thumbnail" required />
            <x-admin.filepond wire:model="photo" class="{{ $errors->has('photo') ? 'is-invalid' : '' }}"
                allowImagePreview imagePreviewMaxHeight="50" allowFileTypeValidation
                acceptedFileTypes="['image/png', 'image/jpg', 'image/jpeg', 'image/*']" allowFileSizeValidation
                maxFileSize="4mb" />
            <x-admin.input-error for="photo" />
        </x-admin.form-group>
        @if ($imgId)
            <x-admin.form-group class="col-lg-6 col-md-6">
                <img src="{{ asset($imgId) }}" style="width: 100px; height:100px;" /><br />
            </x-admin.form-group>
        @endif

        <x-admin.form-group>
            <x-admin.lable value="Title" required />
            <x-admin.input type="text" wire:model.defer="title" placeholder="Title"
                class="{{ $errors->has('title') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="title" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Menu Order" required />
            <x-admin.input type="text" wire:model.defer="menu_order" placeholder="Menu Order"
                class="{{ $errors->has('menu_order') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="menu_order" />
        </x-admin.form-group>


        <x-admin.form-group>
            <x-admin.lable value="Status" required />
            <x-admin.dropdown wire:model.defer="status" placeHolderText="Please select one" autocomplete="off"
                class="{{ $errors->has('status') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($statusList as $status)
                    <x-admin.dropdown-item :value="$status['value']" :text="$status['text']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="status" />
        </x-admin.form-group>
        </div>
        <br />
    </x-slot>
    <x-slot name="actions">
        <x-admin.button type="submit" color="success" wire:loading.attr="disabled">Save</x-admin.button>
        <x-admin.link :href="route('banners.index')" color="secondary">Cancel</x-admin.link>
    </x-slot>
</x-admin.form-section>
