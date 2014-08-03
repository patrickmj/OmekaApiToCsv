<?php

class ItemAdapter extends AbstractRecordAdapter
{
    protected $recordType = 'Item';
    protected $service;
    protected $headings;
    protected $csv;

    public function toCsv()
    {
        //grab the data needed for using update_item or insert_item
        $elementTexts = $this->elementTexts();
        $files = $this->files();
        $fields = array();
        //sort out whether item doesn't have a metadata field
        $elementTexts['files'] = $files;
        foreach ($this->headings as $heading) {
            if (array_key_exists($heading, $elementTexts)) {
                $fields[] = $elementTexts[$heading];
            } else {
                $fields[] = '';
            }
        }
        fputcsv($this->csv, $fields);
    }

    public function externalId()
    {
        return $this->responseData['id'];
    }

    public function setCsv($csv) {
        $this->csv = $csv;
    }
    
    public function setHeadings($headings) {
        $this->headings = $headings;
    }
    /**
     * Process the element text data
     * @param array $responseData
     */
    protected function elementTexts($responseData = null)
    {
        $elementTexts = array();
        if(!$responseData) {
            $responseData = $this->responseData;
        }
        foreach($responseData['element_texts'] as $elTextData) {
            $elId = $elTextData['element']['id'];
            $elName = $elTextData['element']['name'];
            $value = $elTextData['text'];
            if (isset($elementTexts[$elName])) {
                $elementTexts[$elName] = $elementTexts[$elName] . ',' . $value;
            } else {
                $elementTexts[$elName] = $value;
            }
        }
        return $elementTexts;
    }

    /**
     * Parse out and query the data about files for Omeka_File_Ingest_AbstractIngest::factory
     *
     * @return array File data for the file ingester
     */
    protected function files()
    {
        $files = '';
        $itemId = $this->externalId();
        $response = $this->service->get(OMEKA_ENDPOINT . "/files?item=$itemId");
        if($response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody(), true);
        } else {
        }

        foreach($responseData as $fileData) {
            $files .= $fileData['file_urls']['original'] . ',';
        }
        $files = rtrim($files, ',');
        return $files;
    }
}