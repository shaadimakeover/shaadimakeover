<x-vendor-layout title="Dashboard">
    <x-slot name="subHeader">
        <x-admin.sub-header headerTitle="Dashboard">
            <x-slot name="toolbar"></x-slot>
        </x-admin.sub-header>
    </x-slot>
    <div class="kt-portlet">
        <div class="kt-portlet__body  kt-portlet__body--fit">
            <div class="row row-no-padding row-col-separator-xl">
                <div class="col-md-12 col-lg-6 col-xl-3 wizard__box">
                    <div class="kt-widget24">
                        <div class="kt-widget24__details">
                            <div class="kt-widget24__info">
                                <h4 class="kt-widget24__title">
                                    Total Users
                                </h4>
                                <span class="kt-widget24__desc">
                                    Total user available in this system
                                </span>
                            </div>
                            <span class="kt-widget24__stats kt-font-brand">
                                <a href="{{ route('users.index') }}">{{ $count['userCount'] }}</a>
                            </span>
                        </div>
                        <div class="progress progress--sm">
                            <div class="progress-bar kt-bg-brand" role="progressbar"
                                style="width: {{ $count['userCount'] }}%;" aria-valuenow="50" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="kt-widget24__action">
                            <a class="kt-widget24__change" href="{{ route('users.index') }}">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    @endpush
</x-vendor-layout>
