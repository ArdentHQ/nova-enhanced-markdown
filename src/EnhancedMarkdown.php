<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Laravel\Nova\Contracts\Previewable;
use Laravel\Nova\Fields\Markdown\CommonMarkPreset;
use Laravel\Nova\Fields\Markdown\DefaultPreset;
use Laravel\Nova\Fields\Markdown\MarkdownPreset;
use Laravel\Nova\Fields\Markdown\ZeroPreset;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Trix\DeleteAttachments;
use Laravel\Nova\Trix\DetachAttachment;
use Laravel\Nova\Trix\DiscardPendingAttachments;

class EnhancedMarkdown extends Trix implements Previewable
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
     * The built-in presets for the Markdown field.
     *
     * @var string[]
     */
    public $presets = [
        'default'    => DefaultPreset::class,
        'commonmark' => CommonMarkPreset::class,
        'zero'       => ZeroPreset::class,
    ];

    /**
     * Define the preset the field should use. Can be "commonmark", "zero", and "default".
     *
     * @param  string|array<string, mixed>  $preset
     * @return $this
     */
    public function preset(string|array $preset)
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
     * Return a preview for the given field value.
     *
     * @param  string  $value
     * @return string
     */
    public function previewFor($value)
    {
        return $this->renderer()->convert($value);
    }

    /**
     * @return MarkdownPreset
     */
    public function renderer()
    {
        /** @var string $preset */
        $preset = $this->preset;

        /** @var MarkdownPreset $renderer */
        $renderer = new $this->presets[$preset]();

        return $renderer;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
            'preset' => $this->preset,
            'previewFor' => $this->previewFor($this->value ?? ''),
        ]);
    }
}
