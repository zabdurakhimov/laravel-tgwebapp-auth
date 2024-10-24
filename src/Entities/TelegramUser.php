<?php

namespace SMSkin\LaravelTgWebAppAuth\Entities;

class TelegramUser
{
    public string $id;
    public string $firstName;
    public string $lastName;
    public string $username;
    public string $languageCode;
    public bool|null $isPremium;
    public bool $allowWriteToPm;

    public function unSerialize(string $string): self
    {
        $data = json_decode($string);
        $this->id = $data->id;
        $this->firstName = $data->first_name;
        $this->lastName = $data->last_name;
        $this->username = $data->username;
        $this->languageCode = $data->language_code;
        $this->isPremium = $data->is_premium ?? null;
        $this->allowWriteToPm = $data->allows_write_to_pm;
        return $this;
    }

    public function getFullName(): string
    {
        return trim(trim($this->lastName) . ' ' . trim($this->firstName));
    }

    public function getEmail(): string
    {
        return $this->id . '_' . trim($this->username) . '@t.me';
    }
}
