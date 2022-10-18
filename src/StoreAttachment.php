<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreAttachment
{
    /**
     * Create a new invokable instance.
     *
     * @param EnhancedMarkdown $field
     * @return void
     */
    public function __construct(public EnhancedMarkdown $field)
    {
    }

    /**
     * Stores the file on the storage and returns the url.
     *
     * @param Request $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        /** @var string $storageDisk */
        $storageDisk = $this->field->getStorageDisk();

        /** @var string $storageDir */
        $storageDir = $this->field->getStorageDir();

        /** @var UploadedFile $file */
        $file = $request->file('attachment');

        if (is_callable($this->field->fileParserCallback)) {
            call_user_func(
                $this->field->fileParserCallback,
                $this->field,
                $file
            );
        }

        /** @var string $attachment */
        $attachment = $file->store($storageDir, $storageDisk);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($storageDisk);

        return $storage->url($attachment);
    }
}
