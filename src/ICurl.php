<?php
declare(strict_types=1);

namespace LightweightCurl;

interface ICurl
{
    /**
     * Выполняет запроса
     *
     * @param IRequest $request
     *
     * @return IResponse Модель ответа
     */
    public function call(IRequest $request): IResponse;
}
