<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CulturalLevel;
use App\Models\DocumentRequirement;
use App\Models\PortalContent;
use App\Models\Rank;
use Illuminate\Contracts\View\View;

class PublicRegistrationPageController extends Controller
{
    public function __invoke(): View
    {
        return view('public.register', [
            'ranks' => Rank::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'courses' => Course::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'culturalLevels' => CulturalLevel::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'portalContent' => PortalContent::query()->first(),
            'documentRequirements' => DocumentRequirement::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'provinces' => config('military-registration.provinces'),
            'provinceLabels' => config('military-registration.province_labels'),
            'familySituations' => config('military-registration.family_situations'),
            'familySituationLabels' => config('military-registration.family_situation_labels'),
            'genders' => config('military-registration.genders'),
            'genderLabels' => config('military-registration.gender_labels'),
        ]);
    }
}
