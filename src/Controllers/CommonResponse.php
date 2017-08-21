<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 16:02
 */

namespace LpRest\Controllers;

use \Illuminate\Http\JsonResponse;

/**
 * Interface CommonResponse
 * @package LpRest\Controllers CommonResponse
 */
interface CommonResponse
{

    /**
     * @param mixed $body
     * @param bool $success
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    public function responseResult($body, bool $success = true, array $errors = [], int $status = 200):JsonResponse;
}