<?php

mb_internal_encoding("UTF-8");

require_once('library/Communicator.php');

class API_46elks {
    protected $_config = array();

    public function __construct($options = array())
    {
        if (isset($options['config'])) {
            if (is_array($options['config']))
                $this->_config = (object) $options['config'];
            if (is_object($options['config']))
                $this->_config = $options['config'];
        }
    }

    public function apiCall($url, $request = 'GET', $data = array(), $extraHeaders = array())
    {
        $basicAuth = base64_encode($this->_config->api_username . ':' . $this->_config->api_password);
        $extraHeaders['Authorization'] = 'Basic ' . $basicAuth;
        $response = Communicator::httpRequest(preg_replace('/\/$/', '', $this->_config->api_base_url) . '/' . preg_replace('/^\//', '', $url), $request, $data, $extraHeaders);
        return $response;
    }

    public function allocatePhoneNumber()
    {
        return $this->apiCall('/Numbers', 'POST', array('country' => 'se'));
    }

    public function listPhoneNumbers()
    {
        $response = $this->apiCall('/Numbers', 'GET', array('country' => 'se'));
        $json = json_decode($response->result);
        $result = array();
        foreach ($json->data as $number) {
            if ($number->active == 'no')
                continue;
            $result[] = (object) array(
                'country' => $number->country,
                'id' => $number->id,
                'number' => $number->number
            );
        }
        return $result;
    }

    public function modifyPhoneNumber($id, $data = array())
    {
        return $this->apiCall('/Numbers/' . $id, 'POST', $data);
    }

    public function deallocatePhoneNumber($id)
    {
        return $this->modifyPhoneNumber($id, array('active' => 'no'));
    }

    public function sendSmsMessage($from, $to, $message, $delivery_url = null)
    {
        return $this->apiCall('/SMS', 'POST', array('from' => $from, 'to' => $to, 'message' => $message, 'whendelivered' => $delivery_url));
    }

    public function initVoiceCall($from, $to, $actions, $hangup_url = null)
    {
        return $this->apiCall('/Calls', 'POST', array('from' => $from, 'to' => $to, 'voice_start' => $actions, 'whenhangup' => $hangup_url));
    }

    public function pollCall($id)
    {
        return $this->apiCall('/Calls/' . $id, 'GET');

    }
}