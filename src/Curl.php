<?php
declare(strict_types=1);

namespace LightweightCurl;

class Curl implements CurlInterface
{
    /**
     * Выполняет запроса
     *
     * @param RequestInterface $request
     *
     * @return ResultInterface Модель ответа
     *
     * @throws
     */
    public function call(RequestInterface $request): ResultInterface
    {
        $url = $request->getUrl();
        if (empty($url)) {
            throw new CurlException('Url not set');
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
        }

        curl_setopt($curl, CURLINFO_HEADER_OUT, $request->isOutputHeaders());
        curl_setopt($curl, CURLOPT_VERBOSE, $request->isVerbose());

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $request->isCheckSsl());
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $request->isCheckSsl());

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $request->getTimeout());

        if ($request->getUnixSocket()) {
            curl_setopt($curl, CURLOPT_UNIX_SOCKET_PATH, $request->getUnixSocket());
        }

        if ($request->getOutFilename()) {
            $fwout = fopen($request->getOutFilename(),'w');
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, function($curl, $data) use ($fwout) {
                fwrite($fwout, $data);

                return strlen($data);
            });
        }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $request->isFollowLocation());

        $proxy = $request->getProxy();
        if ($proxy) {
            curl_setopt($curl, CURLOPT_PROXYTYPE, $proxy->getType());
            curl_setopt($curl, CURLOPT_PROXY, $proxy->getUrl());
        }

        $headers = $request->getHeaders();
        if ($headers) {
            array_walk($headers, function (&$item, $key) {
                $item = $key . ': ' . $item;
            });

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $data = curl_exec($curl);
        $errorCode = curl_errno($curl);
        if ($errorCode) {
            if ($errorCode === CURLE_COULDNT_CONNECT && $request->getUnixSocket()) {
                $msg = vsprintf(
                    'Check permission to "%s". User /init-docker-socket-right.sh in fpm container',
                    [$request->getUnixSocket(),]
                );
                throw new CurlException($msg);
            }

            throw new CurlException(curl_error($curl));
        }

        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = $request->getOutFilename() ? '' : $data;

        return new Result($httpCode, $data);
    }
}
