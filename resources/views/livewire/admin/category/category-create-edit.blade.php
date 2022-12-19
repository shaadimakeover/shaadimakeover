<x-admin.form-section submit="saveOrUpdate">
    <x-slot name="form">

        <x-admin.form-group>
            <x-admin.lable value="Category Title" required />
            <x-admin.input type="text" wire:model.defer="title" placeholder="Name"
                class="{{ $errors->has('title') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="title" />
        </x-admin.form-group>

        {{-- <x-admin.form-group>
            <x-admin.lable value="Parent Category" />
            <x-admin.dropdown wire:model.defer="parent_id" placeHolderText="Please select parent category"
                autocomplete="off" class="{{ $errors->has('parent_id') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($categoryList as $category)
                    <x-admin.dropdown-item :value="$category['id']" :text="$category['title']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="parent_id" />
        </x-admin.form-group> --}}

        <x-admin.form-group>
            <x-admin.lable value="Short Description" required />
            <x-admin.input type="text" wire:model.defer="short_description" placeholder="Name"
                class="{{ $errors->has('short_description') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="short_description" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Status" required />
            <x-admin.dropdown wire:model.defer="active" placeHolderText="Please select one" autocomplete="off"
                class="{{ $errors->has('active') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($statusList as $status)
                    <x-admin.dropdown-item :value="$status['value']" :text="$status['text']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="active" />
        </x-admin.form-group>

        <x-admin.form-group class="col-lg-12" wire:ignore>
            <x-admin.lable value="Long Description" />
            <textarea x-data x-init="editor = CKEDITOR.replace('long_description');
            editor.on('change', function(event) {
                @this.set('long_description', event.editor.getData());
            })" wire:model.defer="long_description" id="long_description"
                class="form-control {{ $errors->has('long_description') ? 'is-invalid' : '' }}"></textarea>
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Meta Key" />
            <x-admin.input type="text" wire:model.defer="meta_key" placeholder="meta_key"
                class="{{ $errors->has('meta_key') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="meta_key" />
        </x-admin.form-group>
        <x-admin.form-group class="col-lg-6">
            <x-admin.lable value="Thumbnail Image" />
            @if ($model_image)
                <img src="{{ $model_image->getUrl() }}" style="width: 100px; height:100px;" /><br />
            @endif
            <x-admin.filepond wire:model="thumbnail" class="{{ $errors->has('thumbnail') ? 'is-invalid' : '' }}"
                allowImagePreview imagePreviewMaxHeight="50" allowFileTypeValidation
                acceptedFileTypes="['image/png', 'image/jpg', 'image/jpeg']" allowFileSizeValidation
                maxFileSize="4mb" />
            <x-admin.input-error for="thumbnail" />
        </x-admin.form-group>

        <x-admin.form-group class="col-lg-12">
            <x-admin.lable value="Meta Description" />
            <textarea wire:model.defer="meta_description" cols="15" rows="5"
                class="form-control {{ $errors->has('meta_description') ? 'is-invalid' : '' }}"></textarea>
            <x-admin.input-error for="meta_description" />
        </x-admin.form-group>

        </div>
        <br />
    </x-slot>
    <x-slot name="actions">
        <x-admin.button type="submit" color="success" wire:loading.attr="disabled">Save</x-admin.button>
        <x-admin.link :href="route('category.index')" color="secondary">Cancel</x-admin.link>
    </x-slot>
</x-admin.form-section>
