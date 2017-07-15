<?php

namespace App\Services;

use App\Models\Region;
use App\Libraries\Common\Constants;

class RegionService extends BaseService {
    private $regionModel;

    public function __construct() {
        parent::__construct();
        $this->regionModel = new Region();
    }

    public function getSubRegions($parentId=0, $useCache = true) {
        if ($useCache === false) {
            $regionResource = $this->reload(Constants::CACHE_TTL_SIX_HOURS);
        } else {
            $regionResource = $this->remember(Constants::CACHE_TTL_SIX_HOURS);
        }
        /* @var  $resource Region*/
        $resource = $regionResource->setResource($this->regionModel);
        return $resource->getSubRegions($parentId); 
    }

    public function getAllRegions($useCache = true) {
        if ($useCache === false) {
            $regionResource = $this->reload(Constants::CACHE_TTL_SIX_HOURS);
        } else {
            $regionResource = $this->remember(Constants::CACHE_TTL_SIX_HOURS);
        }
        /* @var  $resource Region*/
        $resource = $regionResource->setResource($this->regionModel);
        return $resource->getAllRegions();
    }

    public function getRegionNameByRegionId($regionId, $useCache = true) {
        if ($useCache === false) {
            $regionResource = $this->reload(Constants::CACHE_TTL_SIX_HOURS);
        } else {
            $regionResource = $this->remember(Constants::CACHE_TTL_SIX_HOURS);
        }
        /* @var  $resource Region*/
        $resource = $regionResource->setResource($this->regionModel);
        return $resource->getRegionNameByRegionId($regionId);
    }
}
