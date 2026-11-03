<?php

namespace App\Actions\User;

use App\Emulator\Contracts\PlayerRepository;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RemoveAllFriends
{
    public function __construct(private readonly PlayerRepository $players) {}

    /**
     * The emulator caches the friend list of online players, so friends can
     * only be removed while the player is offline.
     *
     * @throws ValidationException
     */
    public function execute(User $user): int
    {
        if ($user->online) {
            throw ValidationException::withMessages([
                'friends' => __('You must be offline to remove your friends'),
            ])->errorBag('friends');
        }

        return $this->players->removeAllFriends($user);
    }
}
