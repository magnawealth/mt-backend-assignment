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
        $sql = "id > 2 AND name != 'Accountant'";
        // $this->db->where("tanggal BETWEEN $$first_date AND $second_date");

        // $builder->where(new RawSql($sql));
        return $this->model->db->builder->where('lat', $lat)->findAll();
    }

    public function getByTimeInterval($startTime, $endTime)
    {
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $sql = "BETWEEN `.$startTime.` AND `.$endTime`";
        // return $this->model->db->builder->where(new RawSql($sql))->findAll();
        return $this->model->where('timestamp', $sql)->findAll();
    }

    public function persist($data)
    {
        return $this->model->insertBatch($data);
    }

    public function uploadJson($model)
    {
        // TODO:
        // Parse JSON file into database CodeIgniter4
        // Parse JSON file into databasw PHP 
    }
}