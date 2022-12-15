<x-admin-layout title="Customer Management">
    <x-slot name="subHeader">
            <x-admin.sub-header headerTitle="">
				<x-admin.breadcrumbs>
						<x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
						<x-admin.breadcrumbs-separator />
						<x-admin.breadcrumbs-item href="{{ route('banners.index') }}" value="banner List" />
				</x-admin.breadcrumbs>

			    <x-slot name="toolbar" >
					<a href="{{route('banners.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
						<i class="la la-plus"></i>
						Add New Banner
					</a>
				</x-slot>
			</x-admin.sub-header>
    </x-slot>
	<livewire:admin.banner.listing>
</x-admin-layout>
