<?php
/*
 * This scripts provides a RESTful API for a SHOPTIVE
 * application
 *
 * @author Ludim Sanchez
 */
// namespace Api\Shoptive;

abstract class Api {
    /*
     * User's request data
     * Model requested in the URI. 
     * eg: 
     * Array(
     *     [0] => getStores
     *     [1] => latitude=123.123456,longitude=-92.212348
     *  )
     */
    protected static $request_data = "";
    /*
     * HTTP Content Type Header and JSON
     */
    protected static $application = "application/json";
    /*
     * All posible response code
     * 200 is default it means "OK"
     */
    private $response_code = 200;

    public function __construct()
    {
        self::setRequestData();
    }

    // a este set le falta un algo
    protected function setRequestData()
    {
        $method = $_SERVER["REQUEST_METHOD"];
        #echo $method;
        switch (strtolower($method)) {
            case "get":
                self::$request_data = self::cleanRequest($_GET["PATH_INFO"]);
                break;
            case "post":
                self::$request_data = self::cleanRequest($_POST["PATH_INFO"]);
                break;
            case "delete":
            case "put":
                parse_str(file_get_contents("php://input"), self::$request_data);
                $this->request_data = self::cleanRequest(self::$request_data);
                break;
            default:
                // Method Not Allowed
                self::response("Invalid Method", 405);
                break;
        }
    }

    protected function getRequestData()
    {
        return self::$request_data;
    }

    /*
     * Separete user's request and return in an array
     * 
     */
    protected function cleanRequest($request) 
    {
        return explode("/", addslashes($request));
    }

    /*
     * Make responses with HTTP response codes (status-code)
     */
    public function response($response_code = 200, $content = "")
    {
        if (self::functionExists("http_response_code")) {
            http_response_code($response_code);
        } else {
            header("HTTP/1.1 " . $response_code . " " .
                                        self::requestStatus($response_code));
        }
        header("Content-Type: ". self::$application);
        print self::array2Json($content);
        exit;
    }
    /*
     * Verify if php's version exist
     */
    private function functionExists($function_name)
    {
        if (function_exists($function_name)) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * Returns a text that represents status code
     */
    private function requestStatus($response_code)
    {
        switch ($response_code) {
            case 200: $status = "OK";                       break;
            case 201: $status = "Created";                  break;
            case 202: $status = "Accepted";                 break;
            case 204: $status = "No Content";               break;
            case 302: $status = "Found";                    break;
            case 304: $status = "Not Modified";             break;
            case 401: $status = "Unauthorized";             break;
            case 404: $status = "Not Found";                break;
            case 405: $status = "Method Not Allowed";       break;
            case 500: $status = "Internal Server Error";    break;
            default:  $status = "Unknown http status code"; break;
        }
        return $status;
    }
    /*
     * Allows verify if the first element of request_data
     * exists.
     */
    abstract public function resourceExists($resource);

    public function array2Json($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT);
        //return json_encode($array);
    }
}
