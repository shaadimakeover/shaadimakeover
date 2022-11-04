<x-admin-layout title="CMS Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="Edit {!! $cms->title !!} Page">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item  value="Dashboard" href="{{ route('admin.dashboard') }}" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('cms.index') }}" value="Cms List" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item  value="Edit {!! $cms->title !!} Page" />

            </x-admin.breadcrumbs>
            <x-slot name="toolbar">
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
        <!--begin::Form-->
        {!! Form::model($cms,['route' => ['cms.update',$cms->id],'method' => 'post','class'=>"kt-form kt-form--label-right",'files' => true]) !!}
        @method('PUT')
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-12 form-group">
                    <label class="label-required">Title</label>
                    {{Form::text('title', null, $attributes = ['class' => $errors->has('title') ? 'form-control is-invalid' : 'form-control border-gray-200','placeholder'=>'Title'])}}
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-lg-12 form-group">
                    <label class="label-required">Content</label>
                    {{Form::textarea('text_content', null, $attributes = ['class' => $errors->has('text_content') ? 'form-control is-invalid' : 'form-control border-gray-200','placeholder'=>'Content'])}}
                    @error('text_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{route('cms.index')}}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

    {!! Form::close() !!}
    <!--end::Form-->
    </div>
    <div class="loader-wraper d-none position-fixed w-100">
        <div class="cust-loader m-auto"></div>
    </div>


    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });
    </script>
</x-admin-layout>
