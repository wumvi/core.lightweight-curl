<?php
declare(strict_types = 1);

namespace LightweightCurl;

/**
 * Модель ответа запроса
 */
class Result
{
    /** @var int Код ответа */
    private $httpCode;

    /** @var string Данные ответа */
    private $data;

    /**
     * Result constructor.
     *
     * @param int $httpCode Код ответа
     * @param string $data Данные ответа
     */
    public function __construct(int $httpCode, string $data)
    {
        $this->httpCode = $httpCode;
        $this->data = $data;
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
}
