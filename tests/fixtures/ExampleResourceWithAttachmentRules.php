<?php

declare(strict_types=1);

namespace Tests\fixtures;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;

final class ExampleResourceWithAttachmentRules extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = ExampleModel::class;


    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            EnhancedMarkdown::make('Content', 'content')
                ->attachmentRules('dimensions:min_width=20,min_height:20', 'mimes:jpg'),
        ];
    }
}
