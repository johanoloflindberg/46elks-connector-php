<?php

class Communicator {

    public static function httpRequest($url, $request, $data = array(), $extraHeaders = array(), $timeout = 5)
    {
        $params = $data;
        $streamParams = array('http' => array('method' => $request, 'ignore_errors' => true, 'timeout' => $timeout));

        $streamParams['http']['header'] = "User-Agent: API Communicator/1.00\r\n";

        if ($params !== null) {
            if (is_array($params)) {
                $params = http_build_query($params);
                if ($request == 'POST') {
                    $streamParams['http']['content'] = $params;
                    $streamParams['http']['header'] .= "Content-type: application/x-www-form-urlencoded\r\n";
                } else if ($data && count($data))
                    $url .= '?' . $params;
            } else {
                if ($request == 'POST') {
                    $streamParams['http']['content'] = $params;
                    $streamParams['http']['header'] .= "Content-type: application/x-www-form-urlencoded\r\n";
                }
            }
        }

        foreach ($extraHeaders as $headerKey => $headerValue) {
            $streamParams['http']['header'] .= "{$headerKey}: {$headerValue}\r\n";
        }

        $context = stream_context_create($streamParams);
        $fp = fopen($url, 'rb', false, $context);
        if (!$fp)
            $result = false;
        else {
            $result = stream_get_contents($fp);
        }

        $statusCode = null;
        $status = null;

        if ($result === false) {

        } else {
            $metaData = stream_get_meta_data($fp);
            $statusCode = null;
            foreach ($metaData['wrapper_data'] as $line) {
                if (preg_match('/^HTTP\/1\.[01] (\d{3})\s*(.*)/', $line, $match)) {
                    $statusCode = $match[1];
                    $status = $match[2];
                }
            }
        }
        return (object) array('statusCode' => $statusCode, 'status' => $status, 'result' => $result);
    }
}