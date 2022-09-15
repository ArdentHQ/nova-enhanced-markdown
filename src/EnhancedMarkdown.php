<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Trix\DeleteAttachments;
use Laravel\Nova\Trix\DetachAttachment;
use Laravel\Nova\Trix\DiscardPendingAttachments;

class EnhancedMarkdown extends Trix
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'enhanced-markdown';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Indicates the preset the field should use.
     *
     * @var string|array<string, mixed>
     */
    public $preset = 'default';

    /**
     * Define the preset the field should use. Can be "commonmark", "zero", and "default".
     *
     * @param  string|array<string, mixed>  $preset
     * @return $this
     */
    public function preset($preset)
    {
        $this->preset = $preset;

        return $this;
    }

    /**
     * Specify that file uploads should be allowed.
     *
     * @param  string  $disk
     * @param  string  $path
     * @return $this
     */
    public function withFiles($disk = 'public', $path = '/')
    {
        $this->withFiles = true;

        $this->disk($disk)->path($path);

        $this->attach(new StorePendingAttachment($this))
             ->detach(new DetachAttachment())
             ->delete(new DeleteAttachments($this))
             ->discard(new DiscardPendingAttachments())
             ->prunable();

        return $this;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $serialized = parent::jsonSerialize();

        $serialized['preset'] = $this->preset;

        return $serialized;
    }
}
