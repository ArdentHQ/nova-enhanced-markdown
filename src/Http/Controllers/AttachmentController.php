<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown\Http\Controllers;

use Ardenthq\EnhancedMarkdown\EnhancedMarkdown;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachmentController extends Controller
{
    use ValidatesRequests;

    public function __invoke(NovaRequest $request, string $resource, string $field): string
    {
        /** @var Field&EnhancedMarkdown $novaField */
        $novaField = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($field, static function () {
                            abort(404);
                        });

        $this->validate($request, [
            'attachment' => 'file',
        ]);

        $this->validate($request, [
            'attachment' => $novaField->getAttachmentRules($request),
        ]);

        return call_user_func(
            $novaField->attachCallback,
            $request
        );
    }
}
