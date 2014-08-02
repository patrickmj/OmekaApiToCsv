<?php


abstract class AbstractRecordAdapter
{
    protected $responseData;
    protected $externalId;
    protected $csv;
    protected $service;

    /**
     *
     * @param mixed $responseData Data from an API response, typically JSON, XML, RDF, whatevs
     * @param string $endpointUri The API endpoint being used
     * @param string $recordType The Omeka record type being inserted/updated
     */
    public function __construct($responseData = null )
    {
        $this->responseData = $responseData;
        $this->externalId = $this->externalId();
    }

    public function resetResponseData($responseData)
    {
        $this->responseData = $responseData;
    }

    public function setService($service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        return $this->service;
    }
    
    public function setCsv($csv)
    {
        $this->csv = $csv;
    }
    
    public function getCsv()
    {
        return $this->csv;
    }
    
    abstract public function toCsv();
    
    abstract public function externalId();
}