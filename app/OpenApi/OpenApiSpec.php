<?php

namespace App\OpenApi;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="YWC API",
 *         version="1.0.0",
 *         description="API documentation for YWC platform."
 *     ),
 *     @OA\Server(
 *         url="https://console-ywc.nativeappdev.com",
 *         description="Production server"
 *     ),
 *     @OA\Server(
 *         url="http://127.0.0.1:8000",
 *         description="Development server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT",
 *             description="Enter your token in the format: Bearer {token}"
 *         )
 *     )
 * )
 */
class OpenApiSpec
{
    // This class only holds the OpenAPI annotations.
}

