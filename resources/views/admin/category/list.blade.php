<x-admin-layout title="Category Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('vendors.index') }}" value="Category List" />
            </x-admin.breadcrumbs>

            <x-slot name="toolbar">
                <a href="{{ route('category.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="la la-plus"></i>
                    Add New Category
                </a>
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    @livewire('admin.category.category-list')
</x-admin-layout>
