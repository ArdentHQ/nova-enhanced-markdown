<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Fields\Storable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Contracts\Validation\Rule;

/**
 * @template TValidationRules of array<int, \Stringable|string|\Illuminate\Contracts\Validation\Rule|\Illuminate\Contracts\Validation\InvokableRule|callable>|\Stringable|string|(callable(string, mixed, \Closure):void)
 *
 * @method static static make(mixed $name, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class EnhancedMarkdown extends Markdown implements StorableContract
{
    use Storable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'enhanced-markdown';

    /**
     * The callback that should be executed to store file attachments.
     *
     * @var callable
     */
    public $attachCallback;

    /**
     * The callback that should be executed to store file attachments.
     *
     * @var (callable(\Ardenthq\EnhancedMarkdown\EnhancedMarkdown, \Illuminate\Http\UploadedFile):void)|null
     */
    public $fileParserCallback = null;

    /**
     * The validation rules for file attachments
     *
     * @var TValidationRules
     */
    public $attachmentRules = [];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|\Closure|callable|object|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->attach(new StoreAttachment($this));

        $this->disk('public');
    }

    /**
     * Specify the callback that should be used to store file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function attach(callable $callback)
    {
        $this->attachCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to store file attachments.
     *
     * @param  callable|null  $callback
     * @return $this
     */
    public function parseFile(callable|null $callback)
    {
        $this->fileParserCallback = $callback;

        return $this;
    }

     /**
     * Set the validation rules for the file.
     *
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest):TValidationRules)|TValidationRules  ...$attachmentRules
     * @return $this
     */
    public function attachmentRules($attachmentRules)
    {
        $parameters = func_get_args();

        $this->attachmentRules = (
            $attachmentRules instanceof Rule ||
            $attachmentRules instanceof InvokableRule ||
            is_string($attachmentRules) ||
            count($parameters) > 1
        ) ? $parameters : $attachmentRules;

        return $this;
    }

    /**
     * Get the validation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return TValidationRules
     */
    public function getAttachmentRules(NovaRequest $request)
    {
        return is_callable($this->attachmentRules) ? call_user_func($this->attachmentRules, $request) : $this->attachmentRules;
    }
}
