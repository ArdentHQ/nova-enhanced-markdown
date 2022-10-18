<?php

namespace Ardenthq\EnhancedMarkdown\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachmentController extends Controller
{
    public function __invoke(NovaRequest $request): string
    {
        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Fields\Trix $field */
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        return call_user_func(
            $field->attachCallback, $request
        );
    }
}
