<?php

namespace SMSkin\LaravelTgWebAppAuth\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SMSkin\LaravelTgWebAppAuth\Contracts\IUserRepository;
use SMSkin\LaravelTgWebAppAuth\Entities\TelegramData;
use SMSkin\LaravelTgWebAppAuth\Entities\TelegramUser;

class UserRepository implements IUserRepository
{
    public function getUser(TelegramData $telegramData, bool $autoCreation, string $userModel): Authenticatable|null
    {
        $telegramUser = $telegramData->user;

        $user = $this->getByEmail($userModel, $telegramUser->getEmail());
        if ($user) {
            return $user;
        }

        if (!$autoCreation) {
            return null;
        }

        try {
            return $this->createUser($userModel, $telegramUser);
        } catch (UniqueConstraintViolationException) {
            return $this->getByEmail($userModel, $telegramUser->getEmail());
        }
    }

    private function getByEmail(string $userModel, string $email): User|null
    {
        return app($userModel)::where('email', $email)->first();
    }

    private function createUser(string $userModel, TelegramUser $telegramUser): Authenticatable
    {
        $context = new $userModel();
        $context->name = $telegramUser->getFullName();
        $context->email = $telegramUser->getEmail();
        $context->email_verified_at = now();
        $context->password = Hash::make(time() . '_' . Str::random());
        $context->save();
        return $context;
    }
}
