<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Image implements Rule
{
    protected function isEnabledGDExtensionAndJPEGSupport()
    {
        if (extension_loaded('gd') && function_exists('gd_info')) {
            $gdInfo = gd_info();
            if (!$gdInfo['JPEG Support']) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function passes($attribute, $value)
    {
        $prefixesToRemove = ['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'];
        $cleanedBase64String = str_replace($prefixesToRemove, '', $value);

        // Check if the value can be decoded as base64
        $decodedValue = base64_decode($cleanedBase64String, true);

        // Check if it's a valid image by creating an image from the decoded data
        if ($decodedValue === false) {
            return false;
        }

        $image = imagecreatefromstring($decodedValue);
        // dd($this->isEnabledGDExtensionAndJPEGSupport());

        return $image !== false;
    }

    public function message()
    {
        return 'The :attribute must be a valid base64-encoded image.';
    }
}
