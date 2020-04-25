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

    /**
     * @test
     */
    public function shouldReturnSteveAvatar(): void
    {
        $this->get('/avatar/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_avatar.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnUserAvatarWithSize(): void
    {
        $this->get('/avatar/200/_Cyb3r');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/_Cyb3r_avatar_200.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnSteveSkin(): void
    {
        $this->get('/skin/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_skin.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnSteveSkinBack(): void
    {
        $this->get('/skin-back/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_skin_back.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnSteveHead(): void
    {
        $this->get('/head/Steve');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_head.png'));

        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldDownloadSteveTexture(): void
    {
        $this->get('/download/8667ba71b85a4004af54457a9734eed7');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_raw.png'));

        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldGenerateIsometricAvatar(): void
    {
        $this->get('/head/d59dcabb30424b978f7201d1a076637f');
        $this->assertResponseOk();
        $this->assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function shouldGenerateAvatarUsingDifferentUuidFormat(): void
    {
        $this->get('/avatar/d59dcabb-3042-4b97-8f72-01d1a076637f');
        $this->assertResponseOk();
        $this->assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function shouldFailUpdatingUnExistingUser(): void
    {
        $this->get('/update/d59dcabb30424b978f7201ffffffffff');

        $expectedStatusCode = 404;
        $expectedContentType = 'application/json';
        $this->assertEquals($expectedContentType, $this->response->headers->get('Content-Type'));
        $this->assertEquals($expectedStatusCode, $this->response->getStatusCode());
    }
}
