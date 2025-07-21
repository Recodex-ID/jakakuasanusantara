<?php

namespace Tests\Helpers;

class TestImageHelper
{
    /**
     * Generate a valid base64 encoded PNG image for testing.
     * This creates a simple 100x100 white PNG image.
     */
    public static function getValidBase64Image(): string
    {
        // Create a simple 100x100 white PNG image
        $image = imagecreate(100, 100);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        
        // Capture the image data
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        // Clean up
        imagedestroy($image);
        
        return base64_encode($imageData);
    }

    /**
     * Generate an invalid base64 string for testing validation.
     */
    public static function getInvalidBase64Image(): string
    {
        return 'invalid_base64_string';
    }

    /**
     * Generate a valid base64 but not an image for testing.
     */
    public static function getValidBase64NotImage(): string
    {
        return base64_encode('this is not an image file');
    }
}