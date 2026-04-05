<?php

namespace App\Http\Controllers;

use App\Models\PortalContent;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortalBannerImageController extends Controller
{
    public function __invoke(string $type = 'home'): StreamedResponse
    {
        $content = PortalContent::query()->firstOrFail();

        [$path, $name] = match ($type) {
            'course' => [
                $content->course_page_banner_image_path,
                $content->course_page_banner_image_original_name ?? 'course-page-banner-image',
            ],
            'test-taking-staff' => [
                $content->test_taking_staff_page_banner_image_path,
                $content->test_taking_staff_page_banner_image_original_name ?? 'test-taking-staff-page-banner-image',
            ],
            default => [
                $content->banner_image_path,
                $content->banner_image_original_name ?? 'portal-banner-image',
            ],
        };

        abort_unless($path, 404);

        return Storage::disk('local')->response(
            $path,
            $name,
        );
    }
}
