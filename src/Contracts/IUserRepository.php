<?php

namespace SMSkin\LaravelTgWebAppAuth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use SMSkin\LaravelTgWebAppAuth\Entities\TelegramData;

interface IUserRepository
{
    public function getUser(TelegramData $telegramData, bool $autoCreation, string $userModel): Authenticatable|null;
}
