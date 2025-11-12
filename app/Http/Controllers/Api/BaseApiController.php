<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Trả về response thành công
     */
    protected function successResponse($data = null, $message = 'Thành công', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Trả về response lỗi
     */
    protected function errorResponse($message = 'Có lỗi xảy ra', $errors = null, $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Trả về response không tìm thấy
     */
    protected function notFoundResponse($message = 'Không tìm thấy dữ liệu'): JsonResponse
    {
        return $this->errorResponse($message, null, 404);
    }

    /**
     * Trả về response không có quyền
     */
    protected function unauthorizedResponse($message = 'Không có quyền truy cập'): JsonResponse
    {
        return $this->errorResponse($message, null, 403);
    }

    /**
     * Trả về response validation error
     */
    protected function validationErrorResponse($errors): JsonResponse
    {
        return $this->errorResponse('Dữ liệu không hợp lệ', $errors, 422);
    }
}