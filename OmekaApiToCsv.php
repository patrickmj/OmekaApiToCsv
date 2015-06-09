<?php

define('OMEKA_ENDPOINT', 'http://localhost/whatevomeka/api');

require 'vendor/autoload.php';
require __DIR__ . '/ResponseAdapter/AbstractRecordAdapter.php';
require __DIR__ . '/ResponseAdapter/Omeka/ItemAdapter.php';

use GuzzleHttp\Client;

class OmekaApiToCsvWriter
{
    protected $csv;
    protected $headings;
    protected $service;
    protected $apiParams;
    protected $baseDir;
    
    public function __construct($apiParams = null, $file = null)
    {
        $this->setApiParams($apiParams);
        $baseDir = __DIR__;
        $this->csv = fopen($baseDir . '/files/OmekaApiToCsv.csv', 'w');
        $this->service = new GuzzleHttp\Client();
        $this->writeElements();
        $this->writeItems();
        $this->close();
        
    }
    
    public function close()
    {
        fclose($this->csv);
    }
    
    public function writeElements()
    {
        
        $page = 1;
        $headings = array();
		
		// Add Record ID to $headings
		$headings[] = 'Id';
        do {
            $response = $this->service->get(OMEKA_ENDPOINT . "/elements?page=$page");
            
            if($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody(), true);
                foreach($responseData as $elementData) {
                    $headings[] = $elementData['name'];
                }
            } else {

            }
            $page++;
            //sleep for a little while so we don't look like we're DoS attacking
            usleep(200);
        } while ( $this->hasNextPage($response));
        $headings[] = 'files';
        $this->headings = $headings;
        fputcsv($this->csv, $headings);
    }
    
    public function writeItems()
    {
        $page = 1;
        $adapter = new ItemAdapter();
        $adapter->setHeadings($this->headings);
        $adapter->setCsv($this->csv);
        $adapter->setService($this->service);
        do {
            $response = $this->service->get(OMEKA_ENDPOINT . "/items?page=$page");
            if($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody(), true);
                foreach($responseData as $recordData) {
                    $adapter->resetResponseData($recordData);
                    $adapter->toCsv();
                }
            } else {

            }
            $page++;
            //sleep for a little while so we don't look like we're DoS attacking
            usleep(200);
        } while ( $this->hasNextPage($response));
    }
    
    public function setApiParams($params = null)
    {
        if (is_null($params)) {
            $params = array();
            $params['endpoint'] = 'http://localhost/Omeka/api';
            $params['key'] = '';
        }
        
        $this->apiParams = $params;
    }
    
    protected function hasNextPage($response)
    {
        $linksHeading = $response->getHeader('Link');
        return strpos($linksHeading, 'rel="next"');
    }
}

$csver = new OmekaApiToCsvWriter();
