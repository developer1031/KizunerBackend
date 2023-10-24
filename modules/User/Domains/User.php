<?php

namespace Modules\User\Domains;

class User
{

    /** @var User  */
    private $user;

    /**
     * User constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param string $userId
     * @return \App\User
     */
    public static function find(string $userId)
    {
        $userManager = factory(\App\User::class)->create();
        return $userManager->find($userId);
    }
}
