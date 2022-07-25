<?php

namespace App\Repository;

use App\Models\VesselTrackModel;
use CodeIgniter\Database\RawSql;

class VesselTrackRepository
{
    protected $model;

    public function __construct() {
        $this->model = new VesselTrackModel();
    }

    public function getAll()
    {
        return $this->model->findAll();
    }

    public function getByMMSI($mmsi = [])
    {
        return $this->model->whereIn('mmsi', $mmsi)->findAll(); 
    }

    public function getByPosition($lat, $lon)
    {
        $sql = '`lat` >= (' . $lat . ') AND `lon` < (' . $lon . ')';
        return $this->model->where($sql)->findAll();
    }

    public function getByTimeInterval($startTime, $endTime)
    {
        $sql = "`timestamp` >= UNIX_TIMESTAMP('" . $startTime;
        $sql .= "') AND `timestamp` < UNIX_TIMESTAMP('" . $endTime . "')";
        return $this->model->where($sql)->findAll();
    }

    public function insert($data)
    {
        return $this->model->insert($data);
    }

    public function insertBatch($data)
    {
        return $this->model->insertBatch($data); 
    }
}