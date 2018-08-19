<?php
/**
 * App Core Class
 * determine controller and actions from url
 * URL Format - controller/method/params
 */

class Core
{
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        //print_r($this->getUrl());
        $url = $this->getUrl();

        //Look for controller
        if (isset($url[0]) && file_exists('../app/controllers/'. ucwords($url[0]). '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }

        require_once '../app/controllers/'. $this->currentController. '.php';

        $this->currentController = new $this->currentController();

        //Look for method/action
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        //get params
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }


    /**
     * @return array
     */
    public function getUrl()
    {
        $returnedUrl = [];
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $returnedUrl = explode('/', $url);
        }
        return $returnedUrl;
    }
}

