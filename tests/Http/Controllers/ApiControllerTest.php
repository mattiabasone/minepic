<?php

declare(strict_types=1);

class ApiControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        \DB::beginTransaction();
    }

    public function tearDown(): void
    {
        \DB::rollBack();
        parent::tearDown();
    }

    /** @test */
    public function shouldReturnSteveAvatar(): void
    {
        $this->get('/avatar/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_avatar.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    public function shouldReturnUserAvatarWithSize(): void
    {
        $this->get('/avatar/200/_Cyb3r');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/_Cyb3r_avatar_200.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /** @test */
    public function shouldReturnSteveSkin(): void
    {
        $this->get('/skin/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_skin.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    public function shouldReturnSteveHead(): void
    {
        $this->get('/head/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_head.png'));

        $this->assertEquals($expectedImage, $actualImage);
    }

    /** @test */
    public function shouldDownloadSteveTexture(): void
    {
        $this->get('/download/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_raw.png'));

        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * NOT WORKING.
     */
    public function shouldFailUpdatingUnExistingUser(): void
    {
        $this->get('/update/_Cyb3r_Mega_Fail');

        $expectedStatusCode = 404;
        $expectedContentType = 'application/json';
        dump($this->response->content());
        $this->assertEquals($expectedContentType, $this->response->headers->get('Content-Type'));
        $this->assertEquals($expectedStatusCode, $this->response->getStatusCode());
    }
}
