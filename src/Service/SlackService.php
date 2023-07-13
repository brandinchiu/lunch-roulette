<?php
/**
 * Created by PhpStorm.
 * User: misfitpixel
 * Date: 9/6/19
 * Time: 10:07 AM
 */

namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

/**
 * Class SlackService
 * @package App\Service
 */
class SlackService
{
    /**
     * @param string $url
     * @param string $message
     * @return mixed
     * @throws \Exception
     */
    public function message(string $url, string $message)
    {
        $ch = curl_init();

        $content = json_encode([
            'text' => $message
        ]);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content)
        ]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        $errorCode = curl_errno($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        switch($errorCode){
            case CURLE_OK:
                break;

            case CURLE_OPERATION_TIMEOUTED:
                throw new \Exception('The request to the api timed out.');

            default:
                throw new \Exception('Error encountered during api request.');
        }

        switch($info['http_code']){
            case Response::HTTP_OK:
            case Response::HTTP_ACCEPTED:
            case Response::HTTP_NO_CONTENT:
                break;

            case Response::HTTP_BAD_REQUEST:
                throw new \Exception('Bad Request');

            case Response::HTTP_NOT_FOUND:
                throw new \Exception('Not Found');

            case Response::HTTP_UNAUTHORIZED:
                throw new \Exception('Unauthorized');
        }

        return $result;
    }
}