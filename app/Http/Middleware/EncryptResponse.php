<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class EncryptResponse
{
    public function handle($request, Closure $next)
    {
        // Obtener la respuesta del siguiente middleware
        $response = $next($request);

        // Obtener el contenido de la respuesta
        $content = $response->getContent();

        // Cifrar el contenido usando Crypt
        $encryptedContent = Crypt::encryptString($content);

        // Crear un arreglo con la data cifrada
        $responseData = [
            'data' => $encryptedContent
        ];

        // Retornar un JSON con la data cifrada
        return response()->json($responseData);
    }
}
