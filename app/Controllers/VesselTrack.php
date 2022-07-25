<?php

namespace App\Controllers;

use App\Factory\VesselTrackFactory;
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
    protected $vesselFactory;

    public function __construct() {
        $this->repository = new VesselTrackRepository();
        $this->vesselService = new VesselTrackService();
        $this->vesselFactory = new VesselTrackFactory();
    }

    public function filter(): ResponseInterface
    {
        // Log request
        log_message('info', 'request to filter records started!');
        // Get request variables
        $request = array_change_key_case($this->request->getVar(), CASE_LOWER);
        // Log request variables
        log_message('info', 'request to filter records with => ' . json_encode($request));

        return 
            (empty($request)) 
                ? $this->failForbidden('Query string/param cannot be empty !')
                : (! (empty($request['mmsi'])) 
                    ? $this->getByMMSI($request['mmsi'])
                    : ((! empty($request['lat']) && (! empty($request['lon'])))
                        ? $this->getByPosition($request['lat'], $request['lon'])
                        : ((! empty($request['starttime'])) && (! empty($request['endtime']))
                            ? $this->getByTimeInterval($request['starttime'], $request['endtime'])
                            : $this->failForbidden('Invalid query string/param !'))));
    }

    public function getAll(): ResponseInterface
    {
        log_message('info', 'request to get all records started!');
        return ($this->repository->getAll()) 
                    ? $this->respond($this->repository->getAll()) 
                    : $this->failNotFound('No data found');
    }
    
    public function getByMMSI($mmsi): ResponseInterface
    {
        // Log request
        log_message('info', 'request to filter records with => ' . json_encode($mmsi));

        $mmsi = explode(',', $mmsi);
        return (count($mmsi) >= 1) 
                            ? $this->respond($this->repository->getByMMSI($mmsi)) 
                            : $this->failForbidden('Content type or body cannot be empty!');
    }

    public function getByPosition($lat, $lon): ResponseInterface
    {
        // Log request
        log_message('info', 
            'request to get records with lat(' . $lat . ') and lon(' . $lon . ') started!');

        $data = $this->repository->getByPosition($lat, $lon);
        return (count($data) >= 1) ? $this->respond($data) : $this->failNotFound('No data found');
    }

    public function getByTimeInterval($startTime, $endTime): ResponseInterface
    {
        // Log request
        log_message('info', 
            'request to get records with startTime(' . $startTime . ') and endTime(' . $endTime . ') started!');

        $data = $this->repository->getByTimeInterval($startTime, $endTime);
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
        $fileMimeType = (!($file->getMimeType())) ? $file->getMimeType() : null ;

        // Save File in WritePath
        $storePath = $file->store();

        // Locate uploaded file
        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', '\\', $storePath);
           
        return (isset($file))  
                    // Check if content type is supported
                    ? (($this->vesselService->isValidContentType($fileMimeType))
                        //Check file validity
                        ? ((is_file($filePath)) 
                            // Process upload by file content type and return array or null
                            ? ((is_array($this->processUploadByFileContentType($filePath, $fileMimeType)))
                                ? $this->processDataFromFileUpload($this->processUploadByFileContentType($filePath, $fileMimeType))
                                // if null -> return error creating array from Json file upload
                                : $this->failForbidden('File content cannot be empty!'))
                            // if file is invalid, throw 404 error
                            : $this->failForbidden('Invalid File. Please try again!'))
                        // Return unsupported content type message
                        : $this->failForbidden('File mime type: ' . $fileMimeType . ' not supported!'))
                    : $this->failForbidden('File field cannot be empty!');
    }

    public function processPostData($postData): ResponseInterface
    {
        return (count($postData) > 1) 
            ? (($this->repository->insertBatch($postData)) 
                ? $this->respondCreated('Data created successfully!')
                : $this->failServerError('Error saving data. Please try again!'))
            : $this->repository->insert($postData);
    }

    public function processDataFromFileUpload($dataFromFile): ResponseInterface
    {

        // return $this->respond($dataFromFile);

        return (count($dataFromFile) > 1)
            // insert array into database
            ? (($this->repository->insertBatch($dataFromFile))
                // return success message if data is stored successful
                ? $this->respondCreated('Data created successfully!')
                // otherwise return error message 
                : $this->failServerError('Error saving data. Please try again!'))
            : $this->repository->insert($dataFromFile);              
    }

    public function processUploadByFileContentType($filePath, $fileMimeType)
    {
        // TODO: 
        // Refactor this method 
        
        switch ($fileMimeType) {
            case 'application/json':
                return $this->vesselFactory->createArrayFromJson($filePath);
            case 'application/ld+json':
                return $this->vesselFactory->createArrayFromJson($filePath);
            case 'application/hal+json':
                return $this->vesselFactory->createArrayFromJson($filePath);
            case 'application/vnd.api+json':
                return $this->vesselFactory->createArrayFromJson($filePath);
            case 'application/xml':
                return $this->vesselFactory->createArrayFromXml($filePath);
            case 'text/csv':
                return $this->vesselFactory->createArrayFromCsv($filePath);
            default:
                return null;
        }
    }
}