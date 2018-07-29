<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 28.07.2018
 * Time: 22:31
 */

namespace App\Services;

use App\Helpers\l;
use App\Models\User;

class UserService
{

    /**
     * @var User $user
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     *  If current user data !== saved in db -> update it
     *
     * @param array $data
     * @return User
     */
    public function actualizeUserInfo(array $data)
    {
        $current_user_data = [
            'uid' => $this->user->uid,
            'name' => $this->user->name,
        ];

        if ($data !== $current_user_data) {
            $this->user->update($data);

            l::debug('User ' . $this->user->id . ' data updated', [
                $current_user_data,
                $data
            ]);
        }

        return $this->user;
    }


}