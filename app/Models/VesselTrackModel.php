<?php 

namespace App\Models;

use CodeIgniter\Model;

class VesselTrackModel extends Model
{
    protected $table = 'vesseltrack';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'mmsi', 
        'status', 
        'station', 
        'speed', 
        'lon', 
        'lat', 
        'course', 
        'heading', 
        'rot', 
        'timestamp'
    ];
}