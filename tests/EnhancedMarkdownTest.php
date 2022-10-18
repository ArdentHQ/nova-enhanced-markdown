<?php

declare(strict_types=1);

use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;
use Ardenthq\EnhancedMarkdown\StoreAttachment;

it('creates an instance', function () {
    $field = new EnhancedMarkdown('content');

    expect($field->component)->toBe('enhanced-markdown');

    expect($field->jsonSerialize())->toMatchArray([
        'uniqueKey' => 'content-default-enhanced-markdown',
        'attribute' => 'content',
        'component' => 'enhanced-markdown',
    ]);
});

it('accepts files', function () {
    $field = new EnhancedMarkdown('content');

    expect($field->attachCallback)->toBeInstanceOf(StoreAttachment::class);
});

it('accepts a preset', function () {
    $field = new EnhancedMarkdown('content');

    expect($field->preset)->toBe('default');

    $field->preset('commonmark');

    expect($field->preset)->toBe('commonmark');
});

it('generate previews', function () {
    $field = new EnhancedMarkdown('content');

    expect($field->previewFor('**markdown**'))->toContain('<p><strong>markdown</strong></p>');
});
