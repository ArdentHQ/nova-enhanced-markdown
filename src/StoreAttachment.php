<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
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
            // Some parses, like the ones the spatie media library used, doesnt
            // affect the original instance, so we dont neccesary need to return
            // anything. In some other cases the user may want to return an updated
            // instance of the file that is going to be stored.
            $result = call_user_func(
                $this->field->fileParserCallback,
                $this->field,
                $file
            );

            // We only replace the file if the parser callback returned a file
            // that can be stored with the storage disk.
            if ($this->isStorable($result)) {
                $file = $result;
            }
        }

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($storageDisk);

        /** @var string $attachmentPath */
        $attachmentPath = $storage->putFile($storageDir, $file);

        return $storage->url($attachmentPath);
    }

    private function isStorable(mixed $value): bool
    {
        return $value instanceof UploadedFile
            || $value instanceof File
            || is_string($value);
    }
}
