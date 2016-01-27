<html>
<body bgcolor="#E6E6FA">
<h1>Shoptive!</h1>

</body>
</html> 
<?php
require_once 'api.php';
//use Api\Shoptive;
class Rest extends Api {
    public function __construct()
    {
        parent::__construct();
    }
    public function resourceExists($request)
    {
        echo $request;
        $existent_resources = array('getStores', 'getClothes');
        if (!in_array(array_shift($request), $existent_resources)) {
            return 0;
        }
    }
    public function getRequestData()
    {
        return parent::getRequestData();
    }
}

#$_SERVER["REQUEST_METHOD"] = "GET";
#$_GET["PATH_INFO"] = "getStores/coordinate=123.123456,-92.212348";
$rest = new Rest();
$user_request = $rest->getRequestData();
print_r($user_request);
//$resourceExists = $rest->resourceExists();
//$rest->resourceExists($_GET["PATH_INFO"]);
?>