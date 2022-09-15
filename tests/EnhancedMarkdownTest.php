<?php

declare(strict_types=1);

use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;
use Ardenthq\EnhancedMarkdown\StorePendingAttachment;
use Laravel\Nova\Trix\DeleteAttachments;
use Laravel\Nova\Trix\DetachAttachment;
use Laravel\Nova\Trix\DiscardPendingAttachments;

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

	$field->withFiles('disk-name', 'custom-path');

	expect($field->withFiles)->toBeTrue();
	expect($field->prunable)->toBeTrue();

	expect($field->attachCallback)->toBeInstanceOf(StorePendingAttachment::class);
	expect($field->detachCallback)->toBeInstanceOf(DetachAttachment::class);
	expect($field->deleteCallback)->toBeInstanceOf(DeleteAttachments::class);
	expect($field->discardCallback)->toBeInstanceOf(DiscardPendingAttachments::class);
});

it('accepts a preset', function () {
	$field = new EnhancedMarkdown('content');

	expect($field->preset)->toBe('default');

	$field->preset('commonmark');

	expect($field->preset)->toBe('commonmark');
});
