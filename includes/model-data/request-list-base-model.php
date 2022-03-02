<?php
class RequestListBaseModel {
    public $fromDate;
    public $toDate;
    public $skipCount;
    public $maxResultCount;

    public function __construct($fromDate,$toDate,$skipCount,$maxResultCount )
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->skipCount = $skipCount;
        $this->maxResultCount = $maxResultCount;
    }

    public function getParamsModel() {
        return array(
            'FromDate' => $this->fromDate,
            'ToDate' => $this->toDate,
            'SkipCount' => $this->skipCount,
            'MaxResultCount' => $this->maxResultCount,
        );
    }
}