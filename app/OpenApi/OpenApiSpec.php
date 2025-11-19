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
 *         url=L5_SWAGGER_CONST_PRODUCTION_URL,
 *         description="Production Server"
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_DEVELOPMENT_URL,
 *         description="Development Server"
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

