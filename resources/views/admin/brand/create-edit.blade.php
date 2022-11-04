<x-admin-layout title="Brand Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item value="Dashboard" href="{{ route('admin.dashboard') }}" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('brand.index') }}" value="Brand List" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item value="{{ $brand ? 'Edit' : 'Add' }} Brand" />
            </x-admin.breadcrumbs>
            <x-slot name="toolbar">
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    @livewire('admin.brand.brand-create-edit', ['brand' => $brand])
</x-admin-layout>
