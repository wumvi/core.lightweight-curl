<?php
declare(strict_types=1);

namespace LightweightCurl;

/**
 * Настройки запроса
 * @author vk@wumvi.com
 */
interface IRequest
{
    public function getFileForPutRequest(): ?string;

    /**
     * Устанавливаем метод запроса
     * @param string $method Метод запроса
     */
    public function setMethod(string $method): void;

    /**
     * @param string $socket
     */
    public function setUnixSocket(string $socket): void;

    /**
     * @return string
     */
    public function getUnixSocket(): string;

    /**
     * Получаем метод запроса
     * @return string Метод запроса
     */
    public function getMethod(): string;

    /**
     * Устанавливаем настройки проекси
     *
     * @param IProxySettings $proxy Модель настроек
     */
    public function setProxy(IProxySettings $proxy): void;

    /**
     * Получаем настройки прокси
     *
     * @return IProxySettings|null Настройки
     */
    public function getProxy(): ?IProxySettings;

    /**
     * Устанавливаем флаг расширенного вывода
     * @param bool $isVerbose Флаг расширенного вывода
     */
    public function setFlagVerbose(bool $isVerbose): void;

    /**
     * Выводить ли расширенную информацию
     * @return bool Флаг расширенной информации
     */
    public function isVerbose(): bool;

    /**
     * Устанавливаем флаг проверки Ssl
     * @param bool $isCheckSsl Флаг проверки ssl
     */
    public function setFlagCheckSsl(bool $isCheckSsl): void;

    /**
     * Производить ли проверку ssl
     * @return bool Флаг проверки ssl
     */
    public function isCheckSsl(): bool;

    public function setTimeout(int $timeout): void;


    public function getResponseTimeout(): int;

    /**
     * Устанавливаем флаг вывода заголовков
     * @param bool $isOutputHeaders Флаг вывода заголовков
     */
    public function setFlagOutputHeaders(bool $isOutputHeaders): void;

    /**
     * Выводить ли заголовки запроса
     * @return bool Флаг вывода заголовков
     */
    public function isOutputHeaders(): bool;

    /**
     * Установка ContentType
     * @param string $contentType ContentType
     */
    public function setContentType(string $contentType): void;

    /**
     * Получаем ContentType
     * @return string ContentType
     */
    public function getContentType(): string;

    /**
     * Установка Url
     * @param string $url Url
     */
    public function setUrl(string $url): void;

    /**
     * Получаем Url
     * @return string Url
     */
    public function getUrl(): string;

    /**
     * Установка данных запроса
     * @param array|string $data Данные запроса
     */
    public function setData($data): void;

    /**
     * Получаем данные запроса
     * @return array|string Данные запроса
     */
    public function getData();

    /**
     * Устанавливаем какие файлы надо загрузить
     * @param string $name Название параметра
     * @param File $file Массив моделей файлова
     */
    public function addFile(string $name, File $file): void;

    /**
     * @param string $file
     */
    public function setFileForPutRequest(string $file): void;

    /**
     * Добавить заголовок
     * @param string $name Название заголовка
     * @param string $value Значение заголовка
     */
    public function addHeader(string $name, string $value): void;

    public function setHeaders(array $headers): void;

    /**
     * Заголовки запроса
     * @return \string[] Заголовки
     */
    public function getHeaders(): array;

    /**
     * Получаем модель файлов, который нужно загрузить
     * @return File[] Модель файлов
     */
    public function getFiles(): array;

    /**
     * @param string $outFile
     */
    public function setOutFilename(string $outFile): void;

    /**
     * @return string
     */
    public function getOutFilename(): string;


    /**
     * @return bool
     */
    public function isFollowLocation(): bool;

    /**
     * @param bool $isFollow
     */
    public function setFollowLocation(bool $isFollow): void;

    /**
     * @return int
     */
    public function getConnectTimeout(): int;

    public function getEncoding(): string;
}
