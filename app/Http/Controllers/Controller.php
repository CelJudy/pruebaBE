<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mi API en Laravel 13",
    description: "Documentación de la API"
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Servidor Principal'
)]
abstract class Controller
{
    //
}
