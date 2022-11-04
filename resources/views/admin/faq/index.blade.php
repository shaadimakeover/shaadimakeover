<x-admin-layout title="Faq Management">
	<x-slot name="subHeader">
        <x-admin.sub-header headerTitle="">
			<x-admin.breadcrumbs>
				<x-admin.breadcrumbs-item  value="Faq" />
				<x-admin.breadcrumbs-separator />
				<x-admin.breadcrumbs-item  value="List" />
			</x-admin.breadcrumbs>
			<x-slot name="toolbar">
			<a href="{{route('faq.create')}}" class="btn btn-brand btn-icon-sm"><i class="flaticon2-plus"></i>Add New</a>
			</x-slot>
		</x-admin.sub-header>
	</x-slot>
	<livewire:admin.faq.faq-index/>

</x-admin-layout>
