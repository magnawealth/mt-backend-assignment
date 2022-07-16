<?php

namespace App\Factory;

use App\Controllers\BaseController;

class VesselTrackFactory extends BaseController
{
    public function createArrayFromJson($filePath)
    {
        $contents = file_get_contents($filePath);
        return ((string) $contents !== '') ? json_decode($contents, true) : null ;
    }

    public function createArrayFromXml($filePath)
    {
        $contents = file_get_contents($filePath);
        return 
            ((string) $contents !== '') 
                // Convert from xml string into an object, then json
                ? (json_encode(simplexml_load_string($contents)) ? json_decode($contents, true) : null)
                : null ;
    }

    public function createArrayFromCsv($filePath)
    {
        $contents = file_get_contents($filePath);
        return ((string) $contents !== '') ? json_decode($contents, true) : null ;
    }
}