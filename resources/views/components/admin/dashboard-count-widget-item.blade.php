@props(['title' => 'info', 'description'=>'desc','count'=>'0','href'=>'#'])

<div class="kt-widget1__item">
    <div class="kt-widget1__info">
        <h3 class="kt-widget1__title">{{ $title }}</h3>
        <span class="kt-widget1__desc">{{ $description }}</span>
    </div>
    <a href="{{ $href }}"><span class="kt-widget1__number kt-font-brand">{{ $count }}</span></a>
</div>