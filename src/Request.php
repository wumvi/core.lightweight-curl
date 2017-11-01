<?php
declare(strict_types=1);

namespace LightweightCurl;

/**
 * Настройки запроса
 * @author vk@wumvi.com
 */
class Request
{
    private const DEFAULT_TIMEOUT = 5;

    /** Запрос GET */
    const METHOD_GET = 1;

    /** Запроса POST */
    const METHOD_POST = 2;

    /** Запроса PUT */
    const METHOD_PUT = 3;

    /** Content type @see https://ru.wikipedia.org/wiki/Multipart/form-data */
    const CONTENT_TYPE_MULTIPART_FORM_DATA = 'multipart/form-data';

    /** @see Content type */
    const CONTENT_TYPE_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /** @var int Метод запроса */
    private $method = self::METHOD_GET;

    /** @var string Url запроса */
    private $url = '';

    /** @var mixed Данные запроса */
    private $data = [];

    /** @var ProxySettings|null Настроки Proxy */
    private $proxy = null;

    /** @var bool Расширенная информация */
    private $isVerbose = false;

    /** @var bool Проверять ли SSL сертификат */
    private $isCheckSsl = false;

    /** @var bool Выводить ли заголовки запроса */
    private $isOutputHeaders = false;

    /** @var string Выводить ли заголовки запроса */
    private $contentType = self::CONTENT_TYPE_MULTIPART_FORM_DATA;

    /** @var \CURLFile[] Модель файлов для загрзуки */
    private $files = [];

    /** @var string[] */
    private $headers = [];

    /** @var string|null */
    private $fileForPutRequest = null;

    /**
     * @var string
     */
    private $socket = '';

    /** @var string */
    private $outFile = '';

    /**
     * @var int
     */
    private $timeout = self::DEFAULT_TIMEOUT;

    public function getFileForPutRequest(): ?string
    {
        return $this->fileForPutRequest;
    }

    /**
     * Устанавливаем метод запроса
     * @param int $method Метод запроса
     */
    public function setMethod(int $method): void
    {
        $this->method = $method;
    }

    /**
     * @param string $socket
     */
    public function setUnixSocket(string $socket): void
    {
        $this->socket = $socket;
    }

    /**
     * @return string
     */
    public function getUnixSocket(): string
    {
        return $this->socket;
    }

    /**
     * Получаем метод запроса
     * @return int Метод запроса
     */
    public function getMethod(): int
    {
        return $this->method;
    }

    /**
     * Устанавливаем настройки проекси
     * @param ProxySettings $proxy Модель настроек
     */
    public function setProxy(ProxySettings $proxy): void
    {
        $this->proxy = $proxy;
    }

    /**
     * Получаем настройки прокси
     * @return ProxySettings|null Настройки
     */
    public function getProxy(): ?ProxySettings
    {
        return $this->proxy;
    }

    /**
     * Устанавливаем флаг расширенного вывода
     * @param bool $isVerbose Флаг расширенного вывода
     */
    public function setFlagVerbose(bool $isVerbose): void
    {
        $this->isVerbose = $isVerbose;
    }

    /**
     * Выводить ли расширенную информацию
     * @return bool Флаг расширенной информации
     */
    public function isVerbose(): bool
    {
        return $this->isVerbose;
    }

    /**
     * Устанавливаем флаг проверки Ssl
     * @param bool $isCheckSsl Флаг проверки ssl
     */
    public function setFlagCheckSsl(bool $isCheckSsl): void
    {
        $this->isCheckSsl = $isCheckSsl;
    }

    /**
     * Производить ли проверку ssl
     * @return bool Флаг проверки ssl
     */
    public function isCheckSsl(): bool
    {
        return $this->isCheckSsl;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }


    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Устанавливаем флаг вывода заголовков
     * @param bool $isOutputHeaders Флаг вывода заголовков
     */
    public function setFlagOutputHeaders(bool $isOutputHeaders): void
    {
        $this->isOutputHeaders = $isOutputHeaders;
    }

    /**
     * Выводить ли заголовки запроса
     * @return bool Флаг вывода заголовков
     */
    public function isOutputHeaders(): bool
    {
        return $this->isOutputHeaders;
    }

    /**
     * Установка ContentType
     * @param string $contentType ContentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Получаем ContentType
     * @return string ContentType
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Установка Url
     * @param string $url Url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Получаем Url
     * @return string Url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Установка данных запроса
     * @param array $data Данные запроса
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Получаем данные запроса
     * @return array|string Данные запроса
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Устанавливаем какие файлы надо загрузить
     * @param string $name Название параметра
     * @param \CURLFile $file Массив моделей файлова
     */
    public function addFile(string $name, \CURLFile $file): void
    {
        $this->data[$name] = $file;
        $this->method = self::METHOD_POST;
    }

    /**
     * @param string $file
     */
    public function setFileForPutRequest(string $file)
    {
        $this->fileForPutRequest = $file;
    }

    /**
     * Добавить заголовок
     * @param string $name Название заголовка
     * @param string $value Значение заголовка
     */
    public function addHeader(string $name, string $value): void
    {
        $this->headers[] = $name . ': ' . $value;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Заголовки запроса
     * @return \string[] Заголовки
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Получаем модель файлов, который нужно загрузить
     * @return \CURLFile[] Модель файлов
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param string $outFile
     */
    public function setOutFilename(string $outFile): void
    {
        $this->outFile = $outFile;
    }

    /**
     * @return string
     */
    public function getOutFilename(): string
    {
        return $this->outFile;
    }
}
