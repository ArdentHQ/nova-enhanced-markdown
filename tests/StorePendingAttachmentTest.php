<?php

declare(strict_types=1);

use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;
use Ardenthq\EnhancedMarkdown\StorePendingAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function fakeRequestFile($file) {
	// Ensures the `file` method returns the file since the original request
	// does not work with mocked files.
	$requestClass = new class extends Request {
		public $file;

		public function file($key = null, $default = null)
		{
			return $this->file;
		}
	};

	$requestClass->file = $file;

	app()->bind('request', fn () => $requestClass);
}

it('stores the image and returns the path', function () {
	Storage::fake('public');

	$field = new EnhancedMarkdown('content');

	$field->withFiles('public');

	$job = new StorePendingAttachment($field);

	$image = UploadedFile::fake()->image('image.png');

	fakeRequestFile($image);

	$request = app('request')->merge([
		'attachment' => $image,
		'draftId' => '123',
	]);

	$response = $job->__invoke($request);

	expect($response)->toContain('.png', '/storage');
});

it('stores a gif image and returns the path', function () {
	Storage::fake('public');

	$field = new EnhancedMarkdown('content');

	$field->withFiles('public');

	$job = new StorePendingAttachment($field);

	$image = UploadedFile::fake()->image('image.gif');

	fakeRequestFile($image);

	$request = app('request')->merge([
		'attachment' => $image,
		'draftId' => '123',
	]);

	$response = $job->__invoke($request);

	expect($response)->toContain('.gif', '/storage');
});
