<?php
declare(strict_types = 1);

namespace LightweightCurl;

/**
 * Модель ответа запроса
 */
interface IResponse
{
    /**
     * Получаем код ответа
     *
     * @return int Код ответа
     */
    public function getHttpCode(): int;
    /**
     * Получаем данные ответа
     *
     * @return string Данные ответа
     */
    public function getData(): string;
    public function getHeaders(): string;
}
