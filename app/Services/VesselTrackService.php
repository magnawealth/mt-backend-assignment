<?php

namespace App\Services;

class VesselTrackService
{
    public $contentTypeArray = [
        'application/json',
        'application/ld+json',
        'application/hal+json',
        'application/vnd.api+json',
        'application/xml',
        'text/csv'
    ];
    
    public function isValidContentType($contentType)
    {
        if ($contentType == "is found in contentTypeArray") {
            return true;
        }
        else {
            return false;
        }
    }

    public function saveFileUploadToPath($file, $uploadPath)
    {
        # code...
    }

    
}