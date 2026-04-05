<?php

namespace App\Http\Controllers;

use App\Models\PortalContent;
use App\Models\TestTakingStaffDocumentRequirement;
use App\Models\TestTakingStaffRank;
use Illuminate\Contracts\View\View;

class PublicTestTakingStaffPageController extends Controller
{
    public function __invoke(): View
    {
        return view('public.test-taking-staff-register', [
            'portalContent' => PortalContent::query()->first(),
            'ranks' => TestTakingStaffRank::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'documentRequirements' => TestTakingStaffDocumentRequirement::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
        ]);
    }
}
