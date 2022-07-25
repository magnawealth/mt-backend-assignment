<?php

namespace App\Factory;

use App\Controllers\BaseController;
use App\Models\VesselTrackModel;

class VesselTrackFactory extends BaseController
{
    public function __construct() {
        $this->model = new VesselTrackModel();
    }

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
        $csvArray = Array();
        // Open uploaded CSV file with read-only mode
        $file = fopen($filePath, "r");
        $lines = file($filePath);
        
        foreach ($lines as $line) {
            $column = explode(',', $line);
            // Get row data
            $this->model->mmsi   = $column[0];
            $this->model->status  = $column[1];
            $this->model->stationId  = $column[2];
            $this->model->speed = $column[3];
            $this->model->lon = $column[4];
            $this->model->lat = $column[5];
            $this->model->course = $column[6];
            $this->model->heading = $column[7];
            $this->model->rot = $column[8];
            $this->model->timestamp = $column[9]; 

            array_push($csvArray, $this->model);
        }

        // Close opened CSV file
        fclose($file);

        $contents = json_encode($csvArray);
        return ((string) $contents !== '') ? json_decode($contents, true) : null ;
    }
}