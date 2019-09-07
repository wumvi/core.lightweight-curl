<?php
declare(strict_types=1);

namespace LightweightCurl;

use LightweightCurl\Exception\CurlException;
use LightweightCurl\Exception\UnixSocketException;

class Curl implements ICurl
{
    /**
     * Выполняет запроса
     *
     * @param IRequest $request
     *
     * @return IResponse Модель ответа
     *
     * @throws
     */
    public function call(IRequest $request): IResponse
    {
        $url = $request->getUrl();
        if (empty($url)) {
            throw new \InvalidArgumentException('Url is empty');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        switch ($request->getMethod()) {
            case Request::METHOD_PUT:
                curl_setopt($curl, CURLOPT_PUT, true);
                $file = $request->getFileForPutRequest();
                if ($file) {
                    curl_setopt($curl, CURLOPT_INFILE, fopen($file, 'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file));
                }

                break;
            case Request::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, 1);
                $postData = $request->getData();
                if ($request->getContentType() === ContentType::X_WWW_FORM_URLENCODED && is_array($postData)) {
                    $postData = urldecode(http_build_query($postData));
                }

                curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
        }

        curl_setopt_array($curl, [
            CURLINFO_HEADER_OUT => $request->isOutputHeaders(),
            CURLOPT_VERBOSE => $request->isVerbose(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => $request->isCheckSsl(),
            CURLOPT_SSL_VERIFYHOST => $request->isCheckSsl(),
            CURLOPT_CONNECTTIMEOUT => $request->getConnectTimeout(),
            CURLOPT_TIMEOUT => $request->getResponseTimeout(),
            CURLOPT_FOLLOWLOCATION => $request->isFollowLocation(),
        ]);

        if (!empty($request->getUnixSocket())) {
            curl_setopt($curl, CURLOPT_UNIX_SOCKET_PATH, $request->getUnixSocket());
        }

        if (!empty($request->getOutFilename())) {
            $fwout = fopen($request->getOutFilename(), 'w');
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, function ($curl, $data) use ($fwout) {
                fwrite($fwout, $data);

                return strlen($data);
            });
        }

        $proxy = $request->getProxy();
        if ($proxy !== null) {
            curl_setopt($curl, CURLOPT_PROXYTYPE, $proxy->getSocketType());
            curl_setopt($curl, CURLOPT_PROXY, $proxy->getUrl());
        }

        $headers = $request->getHeaders();
        if (!empty($request->getContentType())) {
            $headers['Content-Type'] = $request->getContentType();
        }

        if (!empty($headers)) {
            array_walk($headers, function (&$item, $key) {
                $item = $key . ': ' . $item;
            });

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $data = curl_exec($curl);
        $httpCode = $this->getHttpCode($curl, $request);
        $data = $request->getOutFilename() ? '' : $data;

        return new Response($httpCode, $data);
    }

    /**
     * @param $curl
     * @param IRequest $request
     *
     * @return int
     *
     * @throws
     */
    private function getHttpCode($curl, IRequest $request): int
    {
        $errorCode = curl_errno($curl);
        if ($errorCode === 0) {
            $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return $httpCode;
        }

        if ($errorCode === CURLE_COULDNT_CONNECT && $request->getUnixSocket()) {
            $msg = vsprintf(
                'Check permission to "%s". User /init-docker-socket-right.sh in fpm container',
                [$request->getUnixSocket(),]
            );
            throw new UnixSocketException($msg, UnixSocketException::COULD_NOT_CONNECT);
        }

        throw new CurlException(curl_error($curl), curl_errno($curl));
    }
}
