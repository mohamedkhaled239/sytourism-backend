<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Get full URL for an image path
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public static function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        // Try to get the image domain from config (set by middleware)
        $baseUrl = config('app.image_domain');
        
        // If not set, try to get from request
        if (!$baseUrl && request()) {
            $baseUrl = request()->getSchemeAndHttpHost();
        }
        
        // Fallback to default domain
        if (!$baseUrl) {
            $baseUrl = 'https://darkslateblue-cobra-779637.hostingersite.com';
        }
        
        return $baseUrl . '/storage/' . $imagePath;
    }

    /**
     * Get full URL for location main image
     *
     * @param string|null $mainImage
     * @return string|null
     */
    public static function getLocationMainImageUrl($mainImage)
    {
        return self::getImageUrl($mainImage);
    }

    /**
     * Get full URL for location gallery image
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public static function getLocationGalleryImageUrl($imagePath)
    {
        return self::getImageUrl($imagePath);
    }

    /**
     * Check if image exists
     *
     * @param string|null $imagePath
     * @return bool
     */
    public static function imageExists($imagePath)
    {
        if (!$imagePath) {
            return false;
        }

        return Storage::disk('public')->exists($imagePath);
    }
}
