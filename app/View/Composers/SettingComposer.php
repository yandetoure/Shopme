<?php declare(strict_types=1); 

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class SettingComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $settings = Setting::getSettings();
        $view->with('siteSettings', $settings);
    }
}

