<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown\ImageOptimizers;

use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\Optimizers\BaseOptimizer;

final class ImagickOptimizer extends BaseOptimizer
{
    // @see https://imagemagick.org/script/mogrify.php
    public string $binaryName = 'mogrify';

    public function canHandle(Image $image): bool
    {
        return $image->mime() === 'image/gif';
    }

    public function getCommand(): string
    {
        $optionString = implode(' ', $this->options);

        return "\"{$this->binaryPath}{$this->binaryName}\" {$optionString}"
            .' -write '.escapeshellarg($this->imagePath)
            .' '.escapeshellarg($this->imagePath);
    }
}
