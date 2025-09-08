<?php

namespace App\View\Composers;

use Illuminate\View\View;

class PageDataComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $pageData = [
            'site' => config('landing.site'),
            'hero' => config('landing.hero'),
            'about' => config('landing.about'),
            'contact' => config('landing.contact'),
            'footer' => [
                'company_info' => [
                    'name' => config('landing.site.name'),
                    'description' => config('landing.site.description'),
                ],
                'links' => config('landing.footer.links'),
                'copyright' => config('landing.footer.copyright'),
            ],
            'navigation' => config('landing.navigation'),
        ];
        
        // Pastikan data tidak di-override jika sudah ada dari controller
        $view->with('pageData', $pageData);
    }
}
