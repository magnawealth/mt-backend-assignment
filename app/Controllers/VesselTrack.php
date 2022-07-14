<?php

namespace App\Controllers;

use App\Models\VesselTrackModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use App\Repository\VesselTrackRepository;

class VesselTrack extends ResourceController
{
    use ResponseTrait;

    protected $repository;

    public function __construct() {
        $this->repository = new VesselTrackRepository();
    }

    public function index(): ResponseInterface
    {
        $data = $this->repository->getAll();
        return ($data) ? $this->respond($data) : $this->failNotFound('No data found');
    }
    
    public function getByMMSI(): ResponseInterface
    {
        $mmsi = $this->request->getVar("mmsi") ? explode(',', $this->request->getVar("mmsi")) : 0;
        $data = (count($mmsi) >= 1) ? $this->repository->getByMMSI($mmsi) : 'No data found';
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function getByPosition(): ResponseInterface
    {
        $lat = $this->request->getVar("lat") ? $this->request->getVar("lat") : 0; 
        $lon = $this->request->getVar("lon") ? $this->request->getVar("lon") : 0;
        $data = (!empty($lat) && !empty($lon)) 
                            ? $this->repository->getByPosition($lat, $lon) 
                            : 'No data found';
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function getByTimeInterval(): ResponseInterface
    {
        $startDate = $this->request->getVar("startDate") ? $this->request->getVar("startDate") : 0; 
        $endDate = $this->request->getVar("endDate") ? $this->request->getVar("endDate") : 0;
        $data = (!empty($startDate) && !empty($endDate)) 
                            ? $this->repository->getByTimeInterval($startDate, $endDate) 
                            : 'No Data found';;
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function save(): ResponseInterface
    {
        $data = $this->model->findAll();
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data created successfully'
            ]
          ];
        return $this->respond($response);
    }
}