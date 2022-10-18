<?php

namespace Ardenthq\EnhancedMarkdown\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AttachmentController extends Controller
{
    use ValidatesRequests;

    public function __invoke(NovaRequest $request, string $resource, string $field): string
    {
        /** @var \Laravel\Nova\Fields\Field&\Ardenthq\EnhancedMarkdown\EnhancedMarkdown $novaField */
        $novaField = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($field, function () {
                            abort(404);
                        });

        $this->validate($request, [
            'attachment' => 'file',
        ]);

        $this->validate($request, [
            'attachment' => $novaField->getAttachmentRules($request),
        ]);

        return call_user_func(
            $novaField->attachCallback, $request
        );
    }
}
