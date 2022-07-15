<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use App\Repository\VesselTrackRepository;
use App\Services\VesselTrackService;

class VesselTrack extends ResourceController
{
    use ResponseTrait;

    protected $repository;
    protected $vesselService;

    public function __construct() {
        $this->repository = new VesselTrackRepository();
        $this->vesselService = new VesselTrackService();
    }

    public function getAll(): ResponseInterface
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
        $startTime = $this->request->getVar("startTime") ? $this->request->getVar("startTime") : 0; 
        $endTime = $this->request->getVar("endTime") ? $this->request->getVar("endTime") : 0;
        $data = (!empty($startTime) && !empty($endTime)) 
                            ? $this->repository->getByTimeInterval($startTime, $endTime) 
                            : 'No Data found';
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function postData(): ResponseInterface
    {
        // $getJSON = $this->request->getJSON();
        // $type = $this->request->getHeaderLine('Content-Type');
        // $isset = isset($getJSON);
        // $request = $this->request;
        // $getContentType = 'application/abc';

        // Get json data and content type
        $data = $this->request->getJSON();
        $getContentType = $this->request->getHeaderLine('Content-Type');

        //Process data if data & getContentType is not empty
        return (isset($data) && !empty($getContentType)) 
                    ? (($this->vesselService->isValidContentType($getContentType))
                        ? $this->processPostData($data)
                        : $this->failForbidden('Content type: ' . $getContentType . ' not supported!')) 
                    : $this->failForbidden('Content type or body cannot be empty!'); 
    }

    public function uploadFile(): ResponseInterface
    {
        // Get uploaded file
        // Save it in a folder
        // Write through the data
        // Persist data to database

        $file = $this->request->getFile('file');
        $fileMimeType = $file->getMimeType();
        $readFile = $file->openFile();
        // $readFile->

        // return (isset($file))  
        //             ? (($this->vesselService->isValidContentType($fileMimeType))
        //                 ? $this->processFileUpload($file)
        //                 : $this->failForbidden('File mime type: ' . $fileMimeType . ' not supported!')) 
        //             : $this->failForbidden('File field cannot be empty!');



        $response = [
            'file'   => $file,
            'fileMimeType'   => $fileMimeType,
            'readFile'   => [ $readFile ],
            
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'File upload successfully!'
            ]
        ];

        return $this->respond($response);

        // $jsonFile = $this->request->getPost("json");
        // $upload[] = $this->repository->uploadJson($jsonFile);
        // return ($upload === 'success') 
        //                         ? $this->respondCreated('File upload successfully!') 
        //                         : $this->failForbidden('Error Upload');

        //If file != fileFormatArray
        //return BadRequest('Format ')

    }

    public function processPostData($data): ResponseInterface
    {
        return ($this->repository->persist($data)) 
            ? $this->respondCreated('Data created successfully!')
            : $this->failServerError('Error saving data. Please try again!');
    }

    public function processFileUpload($file): ResponseInterface
    {
        $uploadPath = "";
        $parseToDatabase = "";

        if($parseToDatabase === 'success') {
            try {
                $this->vesselService->saveFileUploadToPath($file, $uploadPath);
            } catch (\Throwable $th) {
                //throw $th; error saving file to DB.
            }
        } else {
            //return error saving data to DB. Please try again
        }

        $data = "Success!";
        return $this->respond($data);

        // return ($this->repository->upload($file)) 
        //     ? $this->respondCreated('Data created successfully!')
        //     : $this->failServerError('Error saving data. Please try again!');
    }
}