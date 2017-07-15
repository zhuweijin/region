<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RegionService;
use App\Libraries\Common\Err;
use App\Libraries\Common\MyException;
use App\Libraries\Common\Util;

class RegionController extends Controller {

    private $regionService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, RegionService $regionService) {
        parent::__construct($request);
        $this->regionService = $regionService;
    }

    /**
     * Refresh Token: userId and accessToken should be involved in header
     * @return array
     * @throws \Exception
     */
    public function regions() {
        $parentId = $this->request->query('parent_id');
        if (empty($parentId)) {
            $regions = $this->regionService->getAllRegions();
        } else {
            $regions = $this->regionService->getSubRegions($parentId);
        }

        return json_encode($regions);
    }
}


