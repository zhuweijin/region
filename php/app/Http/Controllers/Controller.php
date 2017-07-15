<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Services\UserService;
use App\Services\PointService;
use App\Libraries\Common\Err;
use App\Libraries\Common\Util;
use App\Libraries\Template\PropertyDeltaTemplate;
use App\Libraries\Template\PointTransactionTemplate;

class Controller extends BaseController {
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->checkAccessToken();
    }

    public function getAccessToken() {
        return $this->request->header("AccessToken");
    }

    public function checkParam($field, $regex=null, $postData=null) {
        if (empty($postData)) {
            $postData = $this->request->all();
        }

        if (!isset($postData[$field])) {
            xabort(200, Err::ErrMissingParam);
        }

        if (isset($postData[$field]) && empty($postData[$field])) {
            xabort(200, Err::ErrInvalidParam);
        }

        $type = gettype($postData[$field]);
        if ($type == "integer") {
            $regex='/^[0-9]+$/';
        } elseif ($type == "string") {
            if (empty($reqex)) {
                $regex = '/^.*$/';
            }
        }
        
        $match = preg_match($regex, $postData[$field]);
        if (empty($match)) {
            xabort(200, Err::ErrInvalidParam);
        }
    }

    private function checkAccessToken() {
        if($this->isAccessTokenFreeUri() === false && empty($this->getAccessToken())) {
            xabort(200, Err::ErrInvalidSession);
        }
    }

    private function isAccessTokenFreeUri() {
        $AT_Free_URI_PreFix_Array=array(
            '/v1/region',
        );

        $URI=$this->request->url();//$_SERVER['REQUEST_URI'];

        foreach ($AT_Free_URI_PreFix_Array as $prefix) {
            if(strpos($URI, $prefix) !== false){
                return true;
            }
        }

        return false;
    }
}
