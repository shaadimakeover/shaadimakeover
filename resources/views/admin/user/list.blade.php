<x-admin-layout title="User Management">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="User List">
            <x-admin.breadcrumbs>
                <x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
                <x-admin.breadcrumbs-separator />
                <x-admin.breadcrumbs-item href="{{ route('users.index') }}" value="User List" />
            </x-admin.breadcrumbs>

            <x-slot name="toolbar">
                <a href="{{ route('users.create') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="la la-plus"></i>
                    Add New User
                </a>
            </x-slot>
        </x-admin.sub-header>
    </x-slot>
    @livewire('admin.user.user-list')
</x-admin-layout>
