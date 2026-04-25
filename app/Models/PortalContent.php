<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'title',
        'description',
        'banner_image_path',
        'banner_image_original_name',
        'feature_one_title',
        'feature_one_description',
        'feature_two_title',
        'feature_two_description',
        'feature_three_title',
        'feature_three_description',
        'course_page_title',
        'course_page_subtitle',
        'course_page_description',
        'course_page_banner_image_path',
        'course_page_banner_image_original_name',
        'test_taking_staff_page_title',
        'test_taking_staff_page_subtitle',
        'test_taking_staff_page_description',
        'test_taking_staff_page_banner_image_path',
        'test_taking_staff_page_banner_image_original_name',
        'staff_logo_path',
        'staff_logo_original_name',
        'staff_title',
        'staff_subtitle',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'badge' => '',
            'title' => '',
            'description' => '',
            'feature_one_title' => '',
            'feature_one_description' => '',
            'feature_two_title' => '',
            'feature_two_description' => '',
            'feature_three_title' => '',
            'feature_three_description' => '',
            'course_page_title' => '',
            'course_page_subtitle' => '',
            'course_page_description' => '',
            'test_taking_staff_page_title' => '',
            'test_taking_staff_page_subtitle' => '',
            'test_taking_staff_page_description' => '',
            'staff_title' => '',
            'staff_subtitle' => '',
        ];
    }

    public static function firstOrCreateDefault(): self
    {
        return static::query()->first() ?? static::query()->create(static::defaultAttributes());
    }
}
