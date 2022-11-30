<x-admin-layout title="Customer Management">
    <x-slot name="subHeader">
            <x-admin.sub-header headerTitle="">
				<x-admin.breadcrumbs>
						<x-admin.breadcrumbs-item href="{{ route('admin.dashboard') }}" value="Dashboard" />
						<x-admin.breadcrumbs-separator />
						<x-admin.breadcrumbs-item href="{{ route('artists.index') }}" value="artist List" />
				</x-admin.breadcrumbs>

			    <x-slot name="toolbar" >
					{{-- <a href="{{route('artists.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
						<i class="la la-plus"></i>
						Add New artist
					</a> --}}
				</x-slot>
			</x-admin.sub-header>
    </x-slot>
    @livewire('admin.artist.artist-list')
</x-admin-layout>
