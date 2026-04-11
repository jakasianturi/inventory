<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Setting;

class ViewComposer
{

    public function compose(View $view)
    {
        $setting = Setting::first();
        
        $view->with(compact('setting'));
    }
}