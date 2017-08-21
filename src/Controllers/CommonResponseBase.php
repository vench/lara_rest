<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 16:04
 */

namespace LpRest\Controllers;


use Illuminate\Http\JsonResponse;

class CommonResponseBase implements CommonResponse
{

    /**
     * @param mixed $body
     * @param bool $success
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    public function responseResult($body, bool $success = true, array $errors = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success'   => $success,
            'code'      => $status,
            'body'      => $body,
            'errors'    => $errors,
        ], $status);
    }
}