<?php

declare(strict_types=1);

namespace MinepicTests\Http\Controllers;

use Minepic\Cache\UserNotFoundCache;
use Minepic\Events\Account\AccountCreatedEvent;
use Minepic\Models\Account;
use Minepic\Models\AccountStats;
use MinepicTests\TestCase;

class ApiControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \DB::beginTransaction();
        UserNotFoundCache::add('ThisIsAnInvalidAccountName');
    }

    protected function tearDown(): void
    {
        \DB::rollBack();
        parent::tearDown();
    }

    public function testShouldReturnSteveAvatar(): void
    {
        $this->get('/avatar/ThisIsAnInvalidAccountName');
        $actualImage = $this->response->getContent();
        $expectedImage = file_get_contents(base_path('tests/images/steve_avatar.png'));
        self::assertEquals($expectedImage, $actualImage);
    }

    public function testShouldReturnUserAvatarWithSize(): void
    {
        $this->get('/avatar/200/_Cyb3r');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldReturnUserSkinWithoutSize(): void
    {
        $this->get('/skin/_Cyb3r');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldReturnUserSkinBackWithoutSize(): void
    {
        $this->get('/skin-back/_Cyb3r');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldReturnUserSkinWithSize(): void
    {
        $this->get('/skin/200/_Cyb3r');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldReturnUserSkinBackWithSize(): void
    {
        $this->get('/skin-back/200/_Cyb3r');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldReturnSteveSkin(): void
    {
        $this->get('/skin/ThisIsAnInvalidAccountName');
        $actualImage = $this->response->getContent();
        $expectedImage = file_get_contents(base_path('tests/images/steve_skin.png'));
        self::assertEquals($expectedImage, $actualImage);
    }

    public function testShouldReturnSteveSkinBack(): void
    {
        $this->get('/skin-back/ThisIsAnInvalidAccountName');
        $actualImage = $this->response->getContent();
        $expectedImage = file_get_contents(base_path('tests/images/steve_skin_back.png'));
        self::assertEquals($expectedImage, $actualImage);
    }

    public function testShouldReturnSteveHead(): void
    {
        $this->get('/head/ThisIsAnInvalidAccountName');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldDownloadSteveTexture(): void
    {
        $this->get('/download/00000000000000000000000000000000');
        $actualImage = $this->response->getContent();
        $expectedImage = file_get_contents(base_path('tests/images/steve_raw.png'));

        self::assertEquals($expectedImage, $actualImage);
    }

    public function testReturnIsometricAvatar(): void
    {
        $this->get('/head/d59dcabb30424b978f7201d1a076637f');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testReturnDefaultIsometricAvatar(): void
    {
        UserNotFoundCache::add('9bac3f78c4a44f5e841627a674981a5a');
        $this->get('/head/9bac3f78c4a44f5e841627a674981a5a');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
        /*
        $actualImage = $this->response->getContent();
        $expectedImage = \file_get_contents(base_path('tests/images/steve_avatar.png'));
        self::assertEquals($expectedImage, $actualImage);
        */
    }

    public function testReturnAvatarUsingDifferentUuidFormat(): void
    {
        $this->get('/avatar/d59dcabb-3042-4b97-8f72-01d1a076637f');
        $this->assertResponseOk();
        self::assertEquals(
            'image/png',
            $this->response->headers->get('Content-Type')
        );
    }

    public function testShouldFailUpdatingUnExistingUser(): void
    {
        $this->get('/update/d59dcabb30424b978f7201ffffffffff');

        $expectedStatusCode = 404;
        $expectedContentType = 'application/json';
        self::assertEquals($expectedContentType, $this->response->headers->get('Content-Type'));
        self::assertEquals($expectedStatusCode, $this->response->getStatusCode());
    }

    public function testRetrieveNewUser(): void
    {
        // Cleanup
        $steve = Account::whereUsername('MHF_Steve')->first();
        if ($steve !== null) {
            AccountStats::whereUuid($steve->uuid)->delete();
            $steve->delete();
        }

        $this->expectsEvents(AccountCreatedEvent::class);
        $this->get('/avatar/MHF_Steve');

        $expectedStatusCode = 200;
        $expectedContentType = 'image/png';
        self::assertEquals($expectedContentType, $this->response->headers->get('Content-Type'));
        self::assertEquals($expectedStatusCode, $this->response->getStatusCode());
    }
}
