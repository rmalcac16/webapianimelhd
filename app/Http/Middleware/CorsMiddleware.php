<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        // Establece los encabezados CORS
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Verifica si la solicitud es una solicitud de pre-vuelo (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            // Responde con los encabezados CORS para las solicitudes de pre-vuelo
            return response()->json('OK', 200, $headers);
        }

        // Continúa con la solicitud HTTP normal
        $response = $next($request);

        // Agrega los encabezados CORS a la respuesta
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
