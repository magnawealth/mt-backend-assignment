<?php

namespace App\Controllers;

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

    // Return setup view
    // public function index()
    // {
    //     return view('index');
    // }

    public function getAll(): ResponseInterface
    {
        $data = $this->repository->getAll();
        return ($data) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    // public function filter()
    // {
    //     $this->request->getVar("lat");
    //     $this->request->getVar("lon");
    //     $this->request->getVar("endDate");
    //     $this->request->getVar("startDate");
    //     $mmsi = $this->request->getVar("mmsi");
    // }
    
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
                            : 'No Data found';
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function uploadJSON(): ResponseInterface
    {
        // $success = [
        //     'status'   => 201,
        //     'error'    => null,
        //     'messages' => [
        //         'success' => 'File upload successfully!'
        //     ]
        // ];

        //TODO: How to accept different upload format

        $fileFormatArray = [
            'application/json',
            'application/ld+json',
            'application/hal+json',
            'application/vnd.api+json',
            'application/xml',
            'text/csv'
        ];
        // $getHeaders = $this->request->setHeader('Content-Type', $acceptedHeaders);

        $csv = $this->request->setHeader('Content-Type', 'text/csv');
        $xml = $this->request->setHeader('Content-Type', 'application/xml');
        $json = $this->request->setHeader('Content-Type', 'application/json');
        $ldJson = $this->request->setHeader('Content-Type', 'application/ld+json');
        $halJson = $this->request->setHeader('Content-Type', 'application/hal+json');
        $vndJson = $this->request->setHeader('Content-Type', 'application/vnd.api+json');

        $reqJson = $this->request->getPost();
        // $raw_input_stream = $this->input->input_stream;

        // Returns the IP address for the current user. 
        // If the IP address is not valid, the method will return ‘0.0.0.0’:
        // $ip = $this->input->ip_address();

        // Returns the user agent string (web browser) being used by the current user, 
        // or NULL if it’s not available.

        // $this->input->user_agent();

        // $headers = $this->input->request_headers();


        // $Data = json_decode(file_get_contents('php://input'), true);


        // $jsonFile = $this->request->getPost("json");
        // $upload[] = $this->repository->uploadJson($jsonFile);
        // return ($upload === 'success') 
        //                         ? $this->respondCreated('File upload successfully!') 
        //                         : $this->failForbidden('Error Upload');

        //If file != fileFormatArray
        //return BadRequest('File ')

        return $this->respond($reqJson);
    }
}