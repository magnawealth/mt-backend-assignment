<?php

namespace App\Controllers;

use App\Factory\VesselTrackFactory;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use App\Repository\VesselTrackRepository;
use App\Services\VesselTrackService;
use Config\Paths;

use function PHPUnit\Framework\isNull;

class VesselTrack extends ResourceController
{
    use ResponseTrait;

    protected $repository;
    protected $vesselService;
    protected $vesselTrackFactory;

    public function __construct() {
        $this->repository = new VesselTrackRepository();
        $this->vesselService = new VesselTrackService();
        $this->vesselFactory = new VesselTrackFactory();
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
        // Get File info
        $file = $this->request->getFile('file');
        $fileMimeType = $file->getMimeType();

        // Save File in WritePath
        $storePath = $file->store();

        // Locate uploaded file
        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', '\\', $storePath);
           
        return (isset($file))  
                    // Check if content type is supported
                    ? (($this->vesselService->isValidContentType($fileMimeType))
                        //Check file validity
                        ? ((is_file($filePath)) 
                            // Process upload by file content type
                            ? $this->processUploadByFileContentType($filePath, $fileMimeType)
                            // if file is invalid, throw 404 error
                            : $this->failForbidden('Invalid File. Please try again!'))
                        // Return unsupported content type message
                        : $this->failForbidden('File mime type: ' . $fileMimeType . ' not supported!'))
                    : $this->failForbidden('File field cannot be empty!');

        // $response = [
        //     'array'   => $array,
        //     'file'   => $file,
            
        //     'status'   => 201,
        //     'error'    => null,
        //     'messages' => [
        //         'success' => 'File upload successfully!'
        //     ]
        // ];

        // return $this->respond($response);
    }

    public function processPostData($data): ResponseInterface
    {
        return ($this->repository->insertBatch($data)) 
            ? $this->respondCreated('Data created successfully!')
            : $this->failServerError('Error saving data. Please try again!');
    }

    public function processFileUpload($filePath): ResponseInterface
    {
        // TODO: 

        // Create createArrayFromJson
        return 
            // if valid, create array from Json file upload
            (is_array($this->vesselFactory->createArrayFromJson($filePath)))
                    // insert array into database
                ? (($this->repository->insertBatch($this->vesselFactory->createArrayFromJson($filePath)))
                    // return success message if data is stored successful
                    ? $this->respondCreated('Data created successfully!')
                    // otherwise return error message 
                    : $this->failServerError('Error saving data. Please try again!'))
                // return error creating array from Json file upload
                : $this->failForbidden('File content cannot be empty!');
                
        
        // Create convertXmlToArray
        
    }

    public function processUploadByFileContentType($filePath, $fileMimeType)
    {
        // TODO: 
        // Refator this method 
        
        $array = [];

        // if valid, create array from Json file upload
        if (is_array($this->vesselFactory->createArrayFromJson($filePath))) {

        }

        if ($fileMimeType === 'application/json') {
            $array = $this->vesselFactory->createArrayFromJson($filePath);
        }
        if ($fileMimeType === 'application/ld+json') {
            $array = $this->vesselFactory->createArrayFromJson($filePath);
        }
        if ($fileMimeType === 'application/hal+json') {
            $array = $this->vesselFactory->createArrayFromJson($filePath);
        }
        if ($fileMimeType === 'application/vnd.api+json') {
            $array = $this->vesselFactory->createArrayFromJson($filePath);
        }
        if ($fileMimeType === 'application/xml') {
            $array = $this->vesselFactory->createArrayFromXml($filePath);
        }
        if ($fileMimeType === 'text/csv') {
            $array = $this->vesselFactory->createArrayFromCsv($filePath);
        }

        return $array;
    }
}