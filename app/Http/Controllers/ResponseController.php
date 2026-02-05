<?php

namespace App\Http\Controllers;

class ResponseController extends Controller
{

    /**
     * Standard success JSON response.
     */
    public function success($message, $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Standard error JSON response.
     */
    public function error($message, $errors = [], $status = 400)
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
            'message' => $message
        ], $status);
    }
}
