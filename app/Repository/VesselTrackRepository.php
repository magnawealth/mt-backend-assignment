<?php

namespace App\Repository;

use App\Models\VesselTrackModel;
use CodeIgniter\Database\RawSql;

class VesselTrackRepository
{
    protected $model;

    public function __construct() {
        // $builder = $db->table('mytable');
        // $query   = $builder->get();
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

    public function getByTimeInterval($startDate, $endDate)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $sql = "BETWEEN `.$startDate.` AND `.$endDate`";
        // return $this->model->db->builder->where(new RawSql($sql))->findAll();
        return $this->model->where('timestamp', $sql)->findAll();
    }
}