<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

final class JsonDataResponse extends JsonResponse
{
    /**
     * Create a new JSON data response Template.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  array|null  $meta
     * @param  int  $status
     */
    public function __construct(
        mixed $data = null,
        string $message = 'success',
        ?array $meta = null,
        int $status = 200,
    ) {

        // Check status if success or fail based on HTTP Status Code
        $isSuccess = $status >= 200 && $status < 300;

        // Set status to body response based on HTTP Status Code
        $responseData = [
            'status' => $isSuccess ? 'success' : ($status >= 500 ? 'error' : 'fail'),
            'message' => $message,
        ];

        // Inject Meta property if exists
        if ($meta !== null) {
            $responseData['meta'] = $meta;
        }

        // Inject data property if exists
        if ($isSuccess) {
            $responseData['data'] = $data;
        }

        parent::__construct(
            data: $responseData,
            status: $status,
        );
    }
}
