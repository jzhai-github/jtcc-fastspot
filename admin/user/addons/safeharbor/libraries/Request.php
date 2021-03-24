<?php
/**
 * PHP Curl Wrapper for making http request
 *
 * @author      JR Tashjian <jr@packettide.com>
 * @copyright   Copyright (c) 2011, Packet Tide, LLC
 * @link        http://packettide.com
 */
class Request
{
    private $request;
    protected $headers = array();
    protected $options = array();

    private $user_agent;

    private $response;
    private $response_info;

    public function __construct()
    {
        $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Curl/PHP';
    }

    public function get($url, $vars=array())
    {
        if (!empty($vars)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
        }
        return $this->execute('GET', $url);
    }

    public function post($url, $vars=array())
    {
        return $this->execute('POST', $url, $vars);
    }

    public function put($url, $vars=array())
    {
        return $this->execute('PUT', $url, $vars);
    }

    public function delete($url, $vars=array())
    {
        return $this->execute('DELETE', $url, $vars);
    }

    public function set_header($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function set_option($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function set_user_agent($user_agent)
    {
        $this->user_agent = $user_agent;
    }

    private function execute($method, $url, $vars=array())
    {
        $this->request = curl_init();

        // setup url
        curl_setopt($this->request, CURLOPT_URL, $url);

        // setup post data
        if (is_array($vars)) {
            $vars = http_build_query($vars, '', '&');
        }
        if (!empty($vars)) {
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $vars);
        }

        // setup default curl options
        //curl_setopt($this->request, CURLOPT_HEADER, TRUE);
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);

        // setup request method
        switch ($method) {
            case 'GET':
                curl_setopt($this->request, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($this->request, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        // setup request headers
        $headers = array();
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($this->request, CURLOPT_HTTPHEADER, $headers);

        // setup CURL options
        foreach ($this->options as $key => $value) {
            curl_setopt($this->request, constant($key), $value);
        }

        // get response
        $this->response = curl_exec($this->request);
        $this->response_info = curl_getinfo($this->request);


        if (curl_error($this->request)) {
            $this->response['error'] = curl_error($this->request);
        }

        curl_close($this->request);

        return $this->response;
    }

    public function response()
    {
        return $this->response;
    }

    public function response_info()
    {
        return $this->response_info;
    }

    public function response_error()
    {
        if (!empty($this->response['error'])) {
            return $this->response['error'];
        } else {
            return false;
        }
    }
}

/* End of File: Request.php */
