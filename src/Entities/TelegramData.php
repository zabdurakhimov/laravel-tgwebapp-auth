<?php

namespace SMSkin\LaravelTgWebAppAuth\Entities;

use Carbon\Carbon;

class TelegramData
{
    public string $source;
    public array $sourceData;
    public string $queryId;
    public TelegramUser $user;
    public Carbon $authDate;
    public string $hash;

    public function unSerialize(string $string): self
    {
        $this->source = $string;
        parse_str(urldecode($string), $data);
        $this->sourceData = $data;

        $this->queryId = $data['query_id'];
        $this->user = (new TelegramUser())->unSerialize($data['user']);
        $this->authDate = Carbon::createFromTimestamp($data['auth_date']);
        $this->hash = $data['hash'];
        return $this;
    }

    public function validate(string $token): bool
    {
        $secretKey = hash_hmac('sha256', $token, 'WebAppData', true);
        $hash = bin2hex(hash_hmac('sha256', $this->prepareCheckString(), $secretKey, true));
        return strcmp($hash, $this->hash) === 0;
    }

    public function prepareCheckString(): string
    {
        $data = collect($this->sourceData)->filter(static function ($value, $index) {
            return $index !== 'hash';
        })->map(static function ($value, $index) {
            return $index . '=' . $value;
        })->sort()->toArray();
        return implode("\n", array_filter($data));
    }
}
