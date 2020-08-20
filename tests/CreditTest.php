<?php


namespace App\Tests;


use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class CreditTest extends TestCase

{

    public function dataProviderForUserCredit() : array
    {

        $user1=(new User())->setPremiumMember(true);
        $user2=(new User())->setPremiumMember(false);
        return [
            [$user1, true],
            [$user2,false],

        ];
    }

    /**
     * @dataProvider dataProviderForUserCredit
     */
    // Check if they can recharge
    public function testCanRecharge(User $user, bool $expected):void{
        self::assertEquals($expected, $user->canRecharge());
    }

}