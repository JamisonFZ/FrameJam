<?php

namespace FrameJam\Core\I18n;

class Translator
{
    private string $locale;
    private array $translations = [];
    private string $path;

    public function __construct(string $locale = 'pt_BR', string $path)
    {
        $this->locale = $locale;
        $this->path = $path ?? __DIR__ . '/../../resources/lang';
        $this->loadTranslations();
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function trans(string $key, array $replace = []): string
    {
        $translation = $this->getTranslation($key);

        if (!empty($replace)) {
            foreach ($replace as $key => $value) {
                $translation = str_replace(':' . $key, $value, $translation);
            }
        }

        return $translation;
    }

    private function getTranslation(string $key): string
    {
        $keys = explode('.', $key);
        $translation = $this->translations;

        foreach ($keys as $key) {
            if (!isset($translation[$key])) {
                return $key;
            }
            $translation = $translation[$key];
        }

        return is_string($translation) ? $translation : $key;
    }

    private function loadTranslations(): void
    {
        $file = $this->path . '/' . $this->locale . '.php';
        if (file_exists($file)) {
            $this->translations = require $file;
        }
    }
} 