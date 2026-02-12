<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Documentation API Bibliothèque",
    description: "Documentation interactive de l'API avec Swagger"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Serveur local"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    //
}
