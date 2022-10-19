<?php

declare(strict_types=1);

namespace Tests\fixtures;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;
use Illuminate\Http\UploadedFile;

final class ExampleResourceWithCustomFileParser extends Resource
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
                ->parseFile(function (EnhancedMarkdown $field, UploadedFile $file) {
                    $file->name = 'avatar.png';
                }),
        ];
    }
}
