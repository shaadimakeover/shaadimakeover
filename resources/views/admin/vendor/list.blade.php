<x-admin-layout title="Vendor Management">
    <x-slot name="subHeader">
            <x-admin.sub-header headerTitle="">
				<x-admin.breadcrumbs>
						<x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
						<x-admin.breadcrumbs-separator />
						<x-admin.breadcrumbs-item href="{{ route('vendors.index') }}" value="Vendor List" />
				</x-admin.breadcrumbs>

			    <x-slot name="toolbar" >
					<a href="{{route('vendors.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
						<i class="la la-plus"></i>
						Add New Vendor
					</a>
				</x-slot>
			</x-admin.sub-header>
    </x-slot>
    @livewire('admin.vendor.vendor-list')
</x-admin-layout>
