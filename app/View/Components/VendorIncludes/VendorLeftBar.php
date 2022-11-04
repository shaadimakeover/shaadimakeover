<?php

namespace App\View\Components\VendorIncludes;

use Illuminate\View\Component;

class VendorLeftBar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.vendor-includes.vendor-left-bar');
    }
}
