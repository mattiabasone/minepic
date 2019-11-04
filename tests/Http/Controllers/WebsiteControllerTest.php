<?php

declare(strict_types=1);

/**
 * Class WebsiteControllerTest.
 */
class WebsiteControllerTest extends TestCase
{
    /** @test */
    public function shouldDisplayHomePage(): void
    {
        $response = $this->get('/');
        $response->assertResponseStatus(200);
    }

    /** @test */
    public function shouldDisplayUserInfoPage(): void
    {
        $users = ['_Cyb3r', 'hackLover', 'RaynLegends', 'xPeppe'];
        $user = array_rand($users);
        $response = $this->get('/user/'.$users[$user]);
        $response->assertResponseStatus(200);
    }

    /** @test */
    public function shouldDisplay404ForUnExistentUser(): void
    {
        $response = $this->get('/user/IHopeThisUserDoesNotExists');
        $response->assertResponseStatus(404);
    }
}
