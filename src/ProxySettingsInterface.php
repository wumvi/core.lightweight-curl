<?php
declare(strict_types = 1);

namespace LightweightCurl;

/**
 * Настройки proxy
 */
interface ProxySettingsInterface
{
    /**
     * Устанавливаем тип прокси
     * @param int $type Тип прокси см ProxySettings:TYPE_*
     */
    public function setType(int $type): void;

    /**
     * Получаем тип проекси
     * @return int Тип прокси
     */
    public function getType(): int;

    /**
     * Устанавливаем Url проекси
     * @param string $url Url proxy
     */
    public function setUrl(string $url): void;

    /**
     * Получаем URL прокси
     * @return string Url проекси
     */
    public function getUrl(): string;
}
