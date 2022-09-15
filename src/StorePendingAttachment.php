<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Ardenthq\EnhancedMarkdown\ImageOptimizers\ImagickOptimizer;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Trix\PendingAttachment;
use Laravel\Nova\Trix\StorePendingAttachment as TrixStorePendingAttachment;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class StorePendingAttachment extends TrixStorePendingAttachment
{
    use ValidatesRequests;

    final public const ARTICLE_IMAGE_MAX_WIDTH = 927;

    /**
     * Attach a pending attachment to the field.
     *
     * @param Request $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'attachment' => 'image|required',
            'draftId'    => 'required',
        ]);

        /** @var string $storageDisk */
        $storageDisk = $this->field->getStorageDisk();

        /** @var string $storageDir */
        $storageDir = $this->field->getStorageDir();

        /** @var UploadedFile $file */
        $file = $request->file('attachment');

        if ($this->isGif($file)) {
            $this->optimizeGif($file);
        } elseif ($this->isImage($file)) {
            $this->resizeImage($file);
        }

        $attachment = $file->store($storageDir, $storageDisk);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($storageDisk);

        return $storage->url(PendingAttachment::create([
            'draft_id'   => $request->input('draftId'),
            'attachment' => $attachment,
            'disk'       => $storageDisk,
        ])->attachment);
    }

    private function isImage(UploadedFile $attachment): bool
    {
        return str_starts_with($attachment->getMimeType() ?? '', 'image');
    }

    private function isGif(UploadedFile $attachment): bool
    {
        return $attachment->getMimeType() === 'image/gif';
    }

    private function optimizeGif(UploadedFile $attachment): void
    {
        $optimizerChain = OptimizerChainFactory::create();

        $optimizerChain->addOptimizer((new ImagickOptimizer([
            '-layers optimize',
            '-fuzz 7%',
        ])));

        $optimizerChain->optimize($attachment->getPathname());
    }

    private function resizeImage(UploadedFile $attachment): void
    {
        $image = Image::load($attachment->getPathname());
        $image->fit(Manipulations::FIT_MAX, self::ARTICLE_IMAGE_MAX_WIDTH, 0);
        $image->optimize();
        $image->save();
    }
}
