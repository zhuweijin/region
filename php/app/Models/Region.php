<?php

namespace App\Models;

class Region extends BaseModel {
    protected $table = 'region';

    protected $dateFormat = 'U';

    protected $primaryKey = 'region_id';
    protected $hidden = ['created_at', 'updated_at'];

    public function getAllRegions() {
        return [
            "province_list" => $this->getRegionsByType(1),
            "city_list"     => $this->getRegionsByType(2),
            "district_list" => $this->getRegionsByType(3),
            "street_list"   => $this->getRegionsByType(4),
        ];
    }

    public function getSubRegions($parentId) {
        $this->table = "region as r";
        $res = $this->select('r.region_id', 'r.parent_id', 'r.region_name')
            ->where('r.parent_id', $parentId)
            ->get();
        $this->table = "region";

        if (empty($res)) {
            return [];
        }

        return $res->toArray();
    }

    public function getRegionNameByRegionId($regionId) {
        $res = $this->select('region_name')
            ->where('region_id', $regionId)
            ->first();

        if (empty($res)) {
            return "";
        }

        return $res->region_name; 
    }

    private function getRegionsByType($regionType) {
        $this->table = "region as r";
        $res = $this->select('r.region_id', 'r.parent_id', 'r.region_name')
            ->where('r.region_type', $regionType)
            ->get();
        $this->table = "region";

        if (empty($res)) {
            return [];
        }

        return $res->toArray();
    }
}
