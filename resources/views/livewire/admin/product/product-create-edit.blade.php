<x-admin.form-section submit="saveOrUpdate">
    <x-slot name="form">

        <x-admin.form-group class="col-lg-12">
            <x-admin.lable value="Product Title" required />
            <x-admin.input type="text" wire:model.defer="title" placeholder="Name"
                class="{{ $errors->has('title') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="title" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Product Category" />
            <x-admin.dropdown wire:model.defer="category_id" placeHolderText="Please select parent category"
                autocomplete="off" class="{{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($categoryList as $category)
                    <x-admin.dropdown-item :value="$category['id']" :text="$category['title']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="category_id" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Product Brand" />
            <x-admin.dropdown wire:model.defer="brand_id" placeHolderText="Please select parent category"
                autocomplete="off" class="{{ $errors->has('brand_id') ? 'is-invalid' : '' }}">
                <x-admin.dropdown-item :value="$blankArr['value']" :text="$blankArr['text']" />
                @foreach ($brandList as $brand)
                    <x-admin.dropdown-item :value="$brand['id']" :text="$brand['title']" />
                @endforeach
            </x-admin.dropdown>
            <x-admin.input-error for="brand_id" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="MRP Price" required />
            <x-admin.input type="text" wire:model.defer="mrp_price" placeholder="Name"
                class="{{ $errors->has('mrp_price') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="mrp_price" />
        </x-admin.form-group>

        <x-admin.form-group>
            <x-admin.lable value="Sell Price" required />
            <x-admin.input type="text" wire:model.defer="sell_price" placeholder="Name"
                class="{{ $errors->has('sell_price') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="sell_price" />
        </x-admin.form-group>

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
            <x-admin.lable value="Meta Key" required />
            <x-admin.input type="text" wire:model.defer="meta_key" placeholder="meta_key"
                class="{{ $errors->has('meta_key') ? 'is-invalid' : '' }}" />
            <x-admin.input-error for="meta_key" />
        </x-admin.form-group>

        <x-admin.form-group class="col-lg-6">
            <x-admin.lable value="Thumbnail Image" required />
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
            <x-admin.lable value="Meta Description" required />
            <textarea wire:model.defer="meta_description" cols="15" rows="5"
                class="form-control {{ $errors->has('meta_description') ? 'is-invalid' : '' }}"></textarea>
            <x-admin.input-error for="meta_description" />
        </x-admin.form-group>

        </div>
        <br />
    </x-slot>
    <x-slot name="actions">
        <x-admin.button type="submit" color="success" wire:loading.attr="disabled">Save</x-admin.button>
        <x-admin.link :href="route('product.index')" color="secondary">Cancel</x-admin.link>
    </x-slot>
</x-admin.form-section>
