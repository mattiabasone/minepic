<?php

declare(strict_types=1);

class JsonControllerTest extends TestCase
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
    public function shouldReturnTypeaheadEntries(): void
    {
        $this->get('/api/v1/typeahead/Cy');
        $this->assertJson($this->response->content());
    }

    /**
     * @test
     */
    public function shouldReturnUserDataUsingUuid(): void
    {
        $this->get('/api/v1/user/d59dcabb30424b978f7201d1a076637f');
        $responseContent = $this->response->content();
        $this->assertJson($responseContent);
        $this->seeJsonStructure(['ok', 'data'], $responseContent);
    }

    /**
     * @test
     */
    public function shouldReturnUserDataUsingUsername(): void
    {
        $this->get('/api/v1/user/_Cyb3r');
        $responseContent = $this->response->content();
        $this->assertJson($responseContent);
        $this->seeJsonStructure(['ok', 'data'], $responseContent);
    }
}
