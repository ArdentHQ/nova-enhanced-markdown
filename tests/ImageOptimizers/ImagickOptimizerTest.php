<?php

declare(strict_types=1);

use Ardenthq\EnhancedMarkdown\ImageOptimizers\ImagickOptimizer;
use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\Image;

it('handles gif files', function () {
    $optimizer = new ImagickOptimizer();

    $gif  = UploadedFile::fake()->image('logo.gif');
    $path = stream_get_meta_data($gif->tempFile)['uri'];

    expect($optimizer->canHandle(new Image($path)))->toBeTrue();
});

it('creates a command', function () {
    $optimizer = new ImagickOptimizer();

    $optimizer->setImagePath('logo.gif');

    expect($optimizer->getCommand())->toEqual('"mogrify"  -write \'logo.gif\' \'logo.gif\'');
});
