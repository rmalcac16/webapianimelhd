<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;

class EncryptResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Obtener el contenido de la respuesta
        $content = $response->getContent();

        // Cifrar el contenido usando Crypt
        $encryptedContent = Crypt::encryptString($content);

        // Establecer el contenido cifrado en la respuesta
        $response->setContent($encryptedContent);

        return $response;
    }
}
