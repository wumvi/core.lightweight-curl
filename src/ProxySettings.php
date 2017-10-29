<?php
declare(strict_types = 1);

namespace LightweightCurl;

/**
 * Настройки proxy
 */
class ProxySettings
{
    /** Socks5 proxy */
    const TYPE_SOCKS5 = CURLPROXY_SOCKS5;

    /** Socks4 proxy */
    const TYPE_SOCKS4 = CURLPROXY_SOCKS4;

    /** Http proxy */
    const TYPE_HTTP = CURLPROXY_HTTP;

    /** @var int Тип прокси */
    private $type = self::TYPE_SOCKS5;

    /** @var string Url proxy */
    private $url = '';

    /**
     * Устанавливаем тип прокси
     * @param int $type Тип прокси см ProxySettings:TYPE_*
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Получаем тип проекси
     * @return int Тип прокси
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Устанавливаем Url проекси
     * @param string $url Url proxy
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Получаем URL прокси
     * @return string Url проекси
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
