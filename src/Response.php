<?php
declare(strict_types = 1);

namespace LightweightCurl;

/**
 * Модель ответа запроса
 */
class Response implements IResponse
{
    /** @var int Код ответа */
    private $httpCode;

    /** @var string Данные ответа */
    private $data;

    /** @var string Headers */
    private $headers;

    /**
     * Result constructor.
     *
     * @param int $httpCode Код ответа
     * @param string $data Данные ответа
     * @param string $headers Заголовки
     */
    public function __construct(int $httpCode, string $data, string $headers)
    {
        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * Получаем код ответа
     *
     * @return int Код ответа
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Получаем данные ответа
     *
     * @return string Данные ответа
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getHeaders(): string
    {
        return $this->headers;
    }
}
