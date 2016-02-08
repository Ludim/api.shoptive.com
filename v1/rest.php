<?php
require_once 'api.php';
require_once 'poi/NearestPointsOfInterest.php';
require_once 'data/login_algolia.php';
require_once 'data/ConnectionDB.php';
require 'vendor/autoload.php';

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
                                    'getStoresByZipCode', 'getStoresAlgolia');
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
        $objPoi = new NearestPointsOfInterest(array('latitude' => $latitude, 'longitude'=>$longitude));
        $poi = $objPoi->getNearestPointsOfInterest();
        //print_r($poi);
        $this->poiWasFound($poi);
    }

    public function poiWasFound($poi)
    {
        if($this->arraySize($poi)) {
            parent::response($response_code = 200, $poi);
        } else {
            parent::response($response_code = 404, array("stores" => 0, 
                                        "message" => "Stores near not found"));
        }
    }

    public function getStoresAlgolia($coordinate)
    {
        $latitude = $this->getLatitude($coordinate);
        $longitude = $this->getLongitude($coordinate);
        $client = new \AlgoliaSearch\Client(APPLICATION_ID, API_KEY);
        $index = $client->initIndex(INDEX_STORES);
        $query = $index->search("",array("aroundLatLng"=>"$latitude,$longitude",
                                                    "aroundRadius"=> 1000));
        if($this->validateNbHits($query)) {
            parent::response($response_code = 200, $this->arrayInformation($query));
        } else {
            parent::response($response_code = 404, array("stores" => 0, 
                                        "message" => "Stores near not found"));
        }
    }

    public function validateNbHits($query)
    {
        //print_r($query);
        if($query["nbHits"] == 0) {
            return 0;
        }
        return 1;
    }

    public function arrayInformation($query)
    {
        $information = array();
        for($i = 0; $i < sizeof($query["hits"]); $i++) {
          $information[$i] = array("name"     => $query["hits"][$i]["name"], 
                                   "address"    => $query["hits"][$i]["address"],
                                   "_geoloc"  => $query["hits"][$i]["_geoloc"],
                                   "zip_code" => $query["hits"][$i]["zip_code"]);
        }
        return $information;
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

    public function getStoresByZipCode($zip_code)
    {
        $objPoi = new NearestPointsOfInterest($zip_code);
        #$poi = new NearestPointsOfInterest(98101);
        $poi = $objPoi->getNearestPointsOfInterest();
        $this->poiWasFound($poi);
    }
}

$rest = new Rest();
$rest->getRequestData();