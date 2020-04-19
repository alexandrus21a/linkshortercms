<?php

namespace App\Actions\User;

use Auth;
use App\User;

class CrupdateUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $data
     * @param User $overlay
     * @return User
     */
    public function execute($data, $user = null)
    {
        if ( ! $user) {
            $user = $this->user->newInstance();
        }

        $attributes = [
            'name' => $data['name'],
            'user_id' => Auth::id(),
        ];

        $user->fill($attributes)->save();

        return $user;
    }
}