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
        $postData = $request->getData();

        switch ($request->getMethod()) {
            case Request::METHOD_PUT:
                $file = $request->getFileForPutRequest();
                if ($file) {
                    curl_setopt($curl, CURLOPT_PUT, true);
                    curl_setopt($curl, CURLOPT_INFILE, fopen($file, 'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file));
                }
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
            case Request::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                if ($request->getContentType() === ContentType::X_WWW_FORM_URLENCODED && is_array($postData)) {
                    $postData = urldecode(http_build_query($postData));
                }

                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
        }

        if (!empty($postData)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }

        $isHeaderOut = empty($request->getOutFilename());

        curl_setopt_array($curl, [
            CURLINFO_HEADER_OUT => $request->isOutputHeaders(),
            CURLOPT_VERBOSE => $request->isVerbose(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => $request->isCheckSsl(),
            CURLOPT_SSL_VERIFYHOST => $request->isCheckSsl(),
            CURLOPT_CONNECTTIMEOUT => $request->getConnectTimeout(),
            CURLOPT_TIMEOUT => $request->getResponseTimeout(),
            CURLOPT_FOLLOWLOCATION => $request->isFollowLocation(),
            CURLOPT_HEADER => $isHeaderOut
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

        if ($request->getEncoding()) {
            curl_setopt($curl, CURLOPT_ENCODING, $request->getEncoding());
        }

        $rawData = curl_exec($curl);
        $httpCode = $this->getHttpCode($curl, $request);

        $headers = '';
        $body = '';
        if ($isHeaderOut) {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headers = substr($rawData, 0, $headerSize);
            $body = substr($rawData, $headerSize);
        }

        curl_close($curl);

        return new Response($httpCode, $body, $headers);
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
