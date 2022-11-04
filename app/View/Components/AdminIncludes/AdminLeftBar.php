<?php

namespace App\View\Components\AdminIncludes;

use Illuminate\View\Component;

class AdminLeftBar extends Component
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
        return view('components.admin-includes.admin-left-bar');
    }
}
