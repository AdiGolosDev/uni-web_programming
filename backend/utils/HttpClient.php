<?php
require_once __DIR__ . '/exceptions.php';
require_once __DIR__ . '/Logger.php';

class HttpClient
{
    public static function get_json($url, $timeout, $friendly_message)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => (int) $timeout,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'PokePlanner/1.0'
        ]);

        $body = curl_exec($curl);
        $error = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($body === false) {
            Logger::error("External request failed: $error ($url)");
            throw new ExternalServiceException($friendly_message);
        }
        if ($status !== 200) {
            Logger::error("External API returned HTTP $status ($url)");
            throw new ExternalServiceException($friendly_message);
        }
        $data = json_decode($body, true);
        if (!is_array($data)) {
            Logger::error("External API returned invalid JSON ($url)");
            throw new ExternalServiceException($friendly_message);
        }
        return $data;
    }
}