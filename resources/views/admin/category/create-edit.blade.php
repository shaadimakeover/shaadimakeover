<x-admin-layout title="Category Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item value="Dashboard" href="{{ route('admin.dashboard') }}" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('category.index') }}" value="Category List" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item value="{{ $category ? 'Edit' : 'Add' }} Category" />
            </x-admin.breadcrumbs>
            <x-slot name="toolbar">
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    @livewire('admin.category.category-create-edit', ['category' => $category])
</x-admin-layout>
