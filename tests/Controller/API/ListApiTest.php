<?php

namespace Tests\Controller\API;

use App\Models\LinkList;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListApiTest extends ApiTestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    public function testUnauthorizedRequest(): void
    {
        $response = $this->getJson('api/v1/lists');

        $response->assertUnauthorized();
    }

    public function testIndexRequest(): void
    {
        $list = factory(LinkList::class)->create();

        $response = $this->getJson('api/v1/lists', $this->generateHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['name' => $list->name],
                ],
            ]);
    }

    public function testMinimalCreateRequest(): void
    {
        $response = $this->postJson('api/v1/lists', [
            'name' => 'Test List',
        ], $this->generateHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test List',
            ]);

        $databaseList = LinkList::first();

        $this->assertEquals('Test List', $databaseList->name);
    }

    public function testFullCreateRequest(): void
    {
        $response = $this->postJson('api/v1/lists', [
            'name' => 'Test List',
            'description' => 'There could be a description here',
            'is_private' => false,
            'check_disabled' => false,
        ], $this->generateHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test List',
            ]);

        $databaseList = LinkList::first();

        $this->assertEquals('Test List', $databaseList->name);
    }

    public function testInvalidCreateRequest(): void
    {
        $response = $this->postJson('api/v1/lists', [
            'name' => null,
            'is_private' => 'hello',
        ], $this->generateHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'is_private',
            ]);
    }

    public function testShowRequest(): void
    {
        $list = factory(LinkList::class)->create();

        $response = $this->getJson('api/v1/lists/1', $this->generateHeaders());

        $expectedLinkApiUrl = 'http://localhost/api/v1/lists/1/links';

        $response->assertStatus(200)
            ->assertJson([
                'name' => $list->name,
                'links' => $expectedLinkApiUrl,
            ]);
    }

    public function testShowRequestNotFound(): void
    {
        $response = $this->getJson('api/v1/lists/1', $this->generateHeaders());

        $response->assertStatus(404);
    }

    public function testUpdateRequest(): void
    {
        $list = factory(LinkList::class)->create();

        $response = $this->patchJson('api/v1/lists/1', [
            'name' => 'Updated List Title',
            'description' => 'Custom Description',
            'is_private' => false,
        ], $this->generateHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated List Title',
            ]);

        $databaseList = LinkList::first();

        $this->assertEquals('Updated List Title', $databaseList->name);
    }

    public function testInvalidUpdateRequest(): void
    {
        $list = factory(LinkList::class)->create();

        $response = $this->patchJson('api/v1/lists/1', [
            //
        ], $this->generateHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    public function testUpdateRequestNotFound(): void
    {
        $response = $this->patchJson('api/v1/lists/1', [
            'name' => 'Updated List Title',
            'description' => 'Custom Description',
            'is_private' => false,
        ], $this->generateHeaders());

        $response->assertStatus(404);
    }

    public function testDeleteRequest(): void
    {
        $list = factory(LinkList::class)->create();

        $response = $this->deleteJson('api/v1/lists/1', [], $this->generateHeaders());

        $response->assertStatus(200);

        $this->assertEquals(0, LinkList::count());
    }

    public function testDeleteRequestNotFound(): void
    {
        $response = $this->deleteJson('api/v1/lists/1', [], $this->generateHeaders());

        $response->assertStatus(404);
    }
}
