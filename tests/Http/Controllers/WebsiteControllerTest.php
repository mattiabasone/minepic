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
        $response = $this->get('/user/_Cyb3r');
        $response->assertResponseStatus(200);
    }

    /** @test */
    public function shouldDisplay404ForUnExistentUser(): void
    {
        $response = $this->get('/user/IHopeThisUserDoesNotExists');
        $response->assertResponseStatus(404);
    }
}
