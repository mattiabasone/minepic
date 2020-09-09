<?php

declare(strict_types=1);

use App\Cache\UserNotFoundCache;

class ApiControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        \DB::beginTransaction();
        UserNotFoundCache::add('ThisIsAnInvalidAccountName');
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
        $this->get('/avatar/ThisIsAnInvalidAccountName');
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
        $this->assertResponseOk();
        $this->assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function shouldReturnSteveSkin(): void
    {
        $this->get('/skin/ThisIsAnInvalidAccountName');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_skin.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnSteveSkinBack(): void
    {
        $this->get('/skin-back/ThisIsAnInvalidAccountName');
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_skin_back.png'));
        $this->assertEquals($expectedImage, $actualImage);
    }

    /**
     * @test
     */
    public function shouldReturnSteveHead(): void
    {
        $this->get('/head/ThisIsAnInvalidAccountName');
        $this->assertResponseOk();
        $this->assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    /**
     * @test
     */
    public function shouldDownloadSteveTexture(): void
    {
        $this->get('/download/00000000000000000000000000000000');
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
