<?php

namespace App\Http\Controllers;

use App\Models\PortalContent;
use Illuminate\Contracts\View\View;

class PublicPortalPageController extends Controller
{
    public function __invoke(): View
    {
        return view('public.portal', [
            'portalContent' => PortalContent::query()->first(),
        ]);
    }
}
