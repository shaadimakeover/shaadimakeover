<x-admin-layout title="Brand Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('brand.index') }}" value="Brand List" />
            </x-admin.breadcrumbs>

            <x-slot name="toolbar">
                <a href="{{ route('brand.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="la la-plus"></i>
                    Add New Brand
                </a>
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    @livewire('admin.brand.brand-list')
</x-admin-layout>
