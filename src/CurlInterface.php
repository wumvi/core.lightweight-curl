<?php
declare(strict_types=1);

namespace LightweightCurl;

interface CurlInterface
{
    /**
     * Выполняет запроса
     *
     * @param RequestInterface $request
     *
     * @return ResultInterface Модель ответа
     */
    public function call(RequestInterface $request): ResultInterface;
}
