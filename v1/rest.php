<?php
require_once 'api.php';
//use Api\Shoptive;
class Rest extends Api {
    public function __construct()
    {
        parent::__construct();
    }
    public function getRequestData()
    {
        $request = parent::getRequestData();
        $resource = $this->resourceExists($request);
        //$function_name = "$this->".$resource[0]."()";
        $object_method_name = strtolower(array_shift($resource));
        $object_method_args = $resource;
        if($this->methodExistsInsideThisClass($object_method_name) > 0) {
            if($this->arraySize($object_method_args) > 0) {
                call_user_func_array(array($this, $object_method_name),$object_method_args);
            } else {
                call_user_func(array($this, $object_method_name));
            }
        } else {
            parent::response($response_code = 405, "Method Not Exists");
        }
    }
    public function resourceExists($request)
    {
        $existent_resources = array('getStores', 'getClothes',
                                    'getStoresByZipCode');
        if($request[0] == "v1") {
            array_shift($request);
        }
        if (!in_array($request[0], $existent_resources)) {
            //echo "no existe\n";
            parent::response($response_code = 405,"Resource does not exist");
            exit;
        }
        return $request;
    }

    public function methodExistsInsideThisClass($method_name)
    {
        return (int) method_exists($this, $method_name);
    }

    public function arraySize($array)
    {
        return count($array);
    }

    public function getStores($coordinate)
    {
        $latitude = $this->getLatitude($coordinate);
        $longitude = $this->getLongitude($coordinate);

        $c = array('coordinate' => array('latitude' => $latitude, 'longitude'=>$longitude ));
        parent::response($response_code = 200, $c);
    }

    public function getLatitude($coordinate)
    {
        preg_match('/=(.*?),/', $coordinate, $output);
        return $output[1];
    }

    public function getLongitude($coordinate)
    {   
        preg_match('/,(.*?)$/', $coordinate, $output);
        return $output[1];
    }

    public function getClothes($clothes)
    {
        echo "getClothes";
    }

}

$rest = new Rest();
$rest->getRequestData();