<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Fields\Storable;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * @var (callable(EnhancedMarkdown, UploadedFile):void)|null
     */
    public $fileParserCallback = null;

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

    //  /**
    //  * Set the validation rules for the field.
    //  *
    //  * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest):TValidationRules)|TValidationRules  ...$rules
    //  * @return $this
    //  */
    // public function rules($rules)
    // {
    //     $parameters = func_get_args();

    //     $this->rules = (
    //         $rules instanceof Rule ||
    //         $rules instanceof InvokableRule ||
    //         is_string($rules) ||
    //         count($parameters) > 1
    //     ) ? $parameters : $rules;

    //     return $this;
    // }

    // /**
    //  * Get the validation rules for this field.
    //  *
    //  * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
    //  * @return array<string, TValidationRules>
    //  */
    // public function getRules(NovaRequest $request)
    // {
    //     return [
    //         $this->attribute => is_callable($this->rules) ? call_user_func($this->rules, $request) : $this->rules,
    //     ];
    // }
}
