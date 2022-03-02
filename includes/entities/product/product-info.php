<?php
class VehicleImage {
    public $imageUrl;
    public $colorId;
    public $colorName;
    public $hexcode;
    public $price;

    public function __construct($imageUrl, $colorId, $colorName, $hexcode, $price)
    {
        $this->imageUrl = $imageUrl;
        $this->colorId = $colorId;
        $this->colorName = $colorName;
        $this->hexcode = $hexcode;
        $this->price = $price;
    }
}

class InternalColorImage {
    public $imageUrl;
    public $iColorId;
    public $iColorName;
    public $iHexcode;

    public function __construct($imageUrl, $iColorId, $iColorName, $iHexcode)
    {
        $this->imageUrl = $imageUrl;
        $this->iColorId = $iColorId;
        $this->iColorName = $iColorName;
        $this->iHexcode = $iHexcode;
    }
}

class OverviewProduct {
    public $bigGroupId;
    public $bigGroupName;
    public $group;

    public function __construct($bigGroupId, $bigGroupName, $group)
    {
        $this->bigGroupId = $bigGroupId;
        $this->bigGroupName = $bigGroupName;
        $this->group = $group;
    }
}

class GroupOverviewProduct {
    public $groupId;
    public $groupName;
    public $groupValue;
    public $detail;

    public function __construct($groupId, $groupName, $groupValue, $detail)
    {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->groupValue = $groupValue;
        $this->detail = $detail;
    }
}

class DetailOverviewProduct {
    public $detailId;
    public $detailName;
    public $detailValue;
    public $detail;

    public function __construct($detailId, $detailName, $detailValue)
    {
        $this->detailId = $detailId;
        $this->detailName = $detailName;
        $this->detailValue = $detailValue;
    }
}