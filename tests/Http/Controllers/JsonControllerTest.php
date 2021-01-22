<?php

declare(strict_types=1);

namespace MinepicTests\Http\Controllers;

use MinepicTests\TestCase;

class JsonControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        \DB::rollBack();
        parent::tearDown();
    }

    public function testShouldReturnTypeaheadEntries(): void
    {
        $this->get('/api/v1/typeahead/Cy');
        $this->assertJson($this->response->content());
    }

    public function testShouldReturnUserDataUsingUuid(): void
    {
        $this->get('/api/v1/user/d59dcabb30424b978f7201d1a076637f');
        $responseContent = $this->response->content();
        $decodedData = \json_decode($responseContent, true);
        $this->assertJson($responseContent);
        $this->assertArrayHasKey('ok', $decodedData);
        $this->assertArrayHasKey('data', $decodedData);
    }

    public function testShouldNotReturnUserDataUsingInvalidUuid(): void
    {
        $this->get('/api/v1/user/d59dcabb30424b978f7201ffffffffff');
        $responseContent = $this->response->content();
        $decodedData = \json_decode($responseContent, true);
        $this->assertResponseStatus(404);
        $this->assertJson($responseContent);
        $this->assertArrayHasKey('ok', $decodedData);
        $this->assertArrayHasKey('message', $decodedData);
    }

    public function testShouldReturnUserDataUsingUsername(): void
    {
        $this->get('/api/v1/user/_Cyb3r');
        $responseContent = $this->response->content();
        $decodedData = \json_decode($responseContent, true);
        $this->assertJson($responseContent);
        $this->assertArrayHasKey('ok', $decodedData);
        $this->assertArrayHasKey('data', $decodedData);
    }

    public function testShouldReturnMostWantedUser(): void
    {
        $this->get('/api/v1/stats/user/most-wanted');
        $responseContent = $this->response->content();
        $decodedData = \json_decode($responseContent, true);
        $this->assertJson($responseContent);
        $this->assertArrayHasKey('ok', $decodedData);
        $this->assertArrayHasKey('data', $decodedData);
    }
}
