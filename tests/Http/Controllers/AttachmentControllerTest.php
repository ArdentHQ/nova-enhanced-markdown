<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Nova;
use Tests\fixtures\ExampleResource;
use Tests\fixtures\ExampleResourceThatReplacesTheFile;
use Tests\fixtures\ExampleResourceWithAttachmentRules;
use Tests\fixtures\ExampleResourceWithCustomFileParser;

it('stores the attachment', function () {
    Nova::resources([ExampleResource::class]);

    Storage::fake('public');

    $response = $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResource::uriKey().'/store/content', [
        'attachment' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertStatus(200);

    expect($response->content())->toBeString();

    expect($response->content())->toEndWith('.jpg');
});

it('validates a file for the attachment', function () {
    Nova::resources([ExampleResource::class]);

    Storage::fake('public');

    $response = $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResource::uriKey().'/store/content', [
        'attachment' => 'a raw text',
    ])->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'attachment' => ['The attachment must be a file.'],
    ]);
});

it('validates the attachment using the attachment rules', function () {
    Nova::resources([ExampleResourceWithAttachmentRules::class]);

    Storage::fake('public');

    $response = $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResourceWithAttachmentRules::uriKey().'/store/content', [
        'attachment' => UploadedFile::fake()->image(name: 'avatar.png', width: 10, height: 10),
    ])->assertUnprocessable();

    $response->assertJsonValidationErrors([
        'attachment' => [
            'The attachment has invalid image dimensions.',
            'The attachment must be a file of type: jpg.',
        ],
    ]);
});

it('uses a custom parser for the files', function () {
    Nova::resources([ExampleResourceWithCustomFileParser::class]);

    Storage::fake('public');

    $response = $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResourceWithCustomFileParser::uriKey().'/store/content', [
        'attachment' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertStatus(200);

    expect($response->content())->toBeString();

    // The original file was a .jpg, but inside the parser the name was replaced
    // to end in .png
    expect($response->content())->toEndWith('.png');
});

it('uses a custom parser that replaces the file', function () {
    Nova::resources([ExampleResourceThatReplacesTheFile::class]);

    Storage::fake('public');

    $response = $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResourceThatReplacesTheFile::uriKey().'/store/content', [
        'attachment' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertStatus(200);

    expect($response->content())->toBeString();

    // The original file was a .jpg, but inside the parser the name was replaced
    // to end in .png
    expect($response->content())->toEndWith('.png');
});

it('returns not found status if resource does not exist', function () {
    $this->postJson('/ardenthq/nova-enhanced-markdown/my-fake-resource/store/content', [
        'attachment' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertNotFound();
});

it('returns not found status if field does not exist', function () {
    Nova::resources([ExampleResource::class]);

    Storage::fake('public');

    $this->postJson('/ardenthq/nova-enhanced-markdown/'.ExampleResource::uriKey().'/store/a-field-that-does-not-exist', [
        'attachment' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertNotFound();
});
