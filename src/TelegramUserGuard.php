<?php

namespace SMSkin\LaravelTgWebAppAuth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use SMSkin\LaravelTgWebAppAuth\Contracts\IUserRepository;
use SMSkin\LaravelTgWebAppAuth\Entities\TelegramData;
use Throwable;

class TelegramUserGuard implements Guard
{
    use GuardHelpers;
    use Macroable;

    protected bool $loggedOut = false;

    /**
     * @var Authenticatable|null
     */
    protected $user;

    public function __construct(
        private readonly Request $request,
        private readonly IUserRepository $userRepository,
        private readonly string $botToken,
        private readonly string $autoCreation,
        private readonly string $userDataHeaderName,
        private readonly string $userModel
    ) {
    }

    public function user(): Authenticatable|null
    {
        if ($this->loggedOut) {
            return null;
        }

        if ($this->user !== null) {
            return $this->user;
        }

        $userData = $this->request->header($this->userDataHeaderName);
        if (!filled($userData)) {
            return null;
        }

        try {
            $telegramData = (new TelegramData())->unSerialize($userData);
        } catch (Throwable $exception) {
            Log::debug('Telegram data deserialize exception', [
                'userData' => $userData,
                'exception' => [
                    'class' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                ],
            ]);
            return null;
        }

        if (!$telegramData->validate($this->botToken)) {
            Log::debug('Telegram data validation failed');
            return null;
        }

        return $this->user = $this->userRepository->getUser($telegramData, $this->autoCreation, $this->userModel);
    }

    public function validate(array $credentials = []): bool
    {
        throw new RuntimeException('Unsupported method');
    }
}
