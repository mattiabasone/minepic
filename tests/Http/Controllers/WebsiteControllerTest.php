<?php

declare(strict_types=1);

namespace MinepicTests\Http\Controllers;

use MinepicTests\TestCase;

class WebsiteControllerTest extends TestCase
{
    public function testShouldDisplayHomePage(): void
    {
        $response = $this->get('/');
        $response->assertResponseStatus(200);
    }

    public function testShouldDisplayUserInfoPage(): void
    {
        $users = ['_Cyb3r', 'hackLover', 'RaynLegends', 'xPeppe'];
        $user = array_rand($users);
        $response = $this->get('/user/'.$users[$user]);
        $response->assertResponseStatus(200);
    }

    public function testShouldDisplay404ForUnExistentUser(): void
    {
        $response = $this->get('/user/IHopeThisUserDoesNotExists');
        $response->assertResponseStatus(404);
    }
}
