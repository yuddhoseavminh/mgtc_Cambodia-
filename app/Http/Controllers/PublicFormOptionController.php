<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CulturalLevel;
use App\Models\DocumentRequirement;
use App\Models\PortalContent;
use App\Models\Rank;
use Illuminate\Http\JsonResponse;

class PublicFormOptionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'ranks' => Rank::query()
                ->where('is_active', true)
                ->ordered()
                ->get(['id', 'name_kh', 'name_en']),
            'courses' => Course::query()
                ->where('is_active', true)
                ->ordered()
                ->get(['id', 'name', 'description', 'duration']),
            'cultural_levels' => CulturalLevel::query()
                ->where('is_active', true)
                ->ordered()
                ->get(['id', 'name']),
            'document_requirements' => DocumentRequirement::query()
                ->where('is_active', true)
                ->ordered()
                ->get(['id', 'name_kh', 'name_en', 'slug']),
            'portal_content' => PortalContent::query()->first(),
            'provinces' => config('military-registration.provinces'),
            'province_labels' => config('military-registration.province_labels'),
            'family_situations' => config('military-registration.family_situations'),
            'family_situation_labels' => config('military-registration.family_situation_labels'),
            'genders' => config('military-registration.genders'),
            'gender_labels' => config('military-registration.gender_labels'),
        ]);
    }
}
