<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;


#[OA\Info(title: "API Livres", version: "1.0")]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer')]
abstract class Controller
{
    //
}
