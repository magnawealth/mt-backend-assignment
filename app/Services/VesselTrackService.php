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
        return (in_array($contentType, $this->contentTypeArray)) ? true : false;
    }
    
}