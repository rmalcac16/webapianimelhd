<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\Models\Anime;
use App\Models\Episode;
use App\Models\Player;

use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $anime, $episode, $player;

    public function __construct(Anime $anime, Episode $episode, Player $player)
    {
        $this->anime = $anime;
        $this->episode = $episode;
        $this->player = $player;
    }

    public function releases(){
        return $this->episode->releases();
    }

    public function anime(Request $request){
        return $this->anime->anime($request);
    }

    public function episode(Request $request){
        return $this->episode->episode($request);
    }

    public function animes(Request $request){
        return $this->anime->animes($request);
    }

    public function search(Request $request){
        return $this->anime->search($request);
    }

    public function simulcast(){
        return $this->anime->simulcast();
    }

    public function latino(){
        return $this->anime->latino();
    }

    public function trending(){
        return $this->anime->trending();
    }

    public function moreview(){
        return $this->anime->moreview();
    }


    public function token(Request $request){
        try {
            $token = md5(uniqid(rand(), true));
            DB::table('tokens')->insert([
                'player_id' => Crypt::decryptString($request->episode_id),
                'token' => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'referrer' => $request->ip()
            ]);
            return response()->json(['token' => $token], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function video(Request $request)
    {
        $link = route('videoLink', ['id' => $request->id]);
        return view('iframe', ['link' => $link]);
    }

    public function videoLink(Request $request)
    {
        if(!$request->headers->has('Referer')) {
            return abort(403, 'Acceso denegado');
        }
        if($request->headers->has('X-Frame-Options')) {
            $xFrameOptions = $request->headers->get('X-Frame-Options');
            if(!in_array($xFrameOptions, ['DENY', 'SAMEORIGIN'])) {
                return abort(403, 'Acceso denegado');
            }
        }
        $referer = $request->headers->get('Referer');
        if(strpos($referer, 'www.animelatinohd.com') === false) {
            return abort(403, 'Acceso denegado');
        }
        $id = Crypt::decryptString($request->id);
        if(!$id) {
            return abort(404, 'ID no definido');
        }
        $player = $this->player->getPlayerById($id);
        if(!$player) {
            return abort(404, 'No encontrado');
        }
        $link = $this->modifyCode($this->getFullUrl($player), 6);

        return redirect()->away($link);
    }

    public function getFullUrl($player){

        switch ($player->server->type) {
            case '0':
                return $player->code;
            case '1':
                return $player->code;
            case '2':
                if (strtolower($player->server->title) == "gamma") {
                    $idVoe = explode("/", $player->code);
                    $idVoe = $idVoe[4];
                    $player->code = $player->server->embed . "e/" . $idVoe;
                }
                return $player->code;
            default:
                return $player->code;
        }
        
    }

    public function modifyCode($originalLink, $numberOfChanges) {
        // Detectar el servidor según el prefijo del enlace original
        $serverPrefix = '';
        if (strpos($originalLink, 'https://voe.sx/e/') === 0) {
            $serverPrefix = 'https://voe.sx/e/';
        } elseif (strpos($originalLink, 'https://filemoon.sx/e/') === 0) {
            $serverPrefix = 'https://filemoon.sx/e/';
        } else {
            // Si el servidor no es reconocido, devolver el enlace original sin modificar
            return $originalLink;
        }

        // Extraer el ID del enlace original
        $originalId = substr($originalLink, strlen($serverPrefix));

        // Dividir el ID en caracteres individuales
        $idCharacters = str_split($originalId);

        // Intercambiar los caracteres del ID según lo especificado
        for ($i = 0; $i < floor($numberOfChanges / 2); $i++) {
            $firstCharIndex = $i;
            $secondCharIndex = count($idCharacters) - 1 - $i;
            $tempChar = $idCharacters[$firstCharIndex];
            $idCharacters[$firstCharIndex] = $idCharacters[$secondCharIndex];
            $idCharacters[$secondCharIndex] = $tempChar;
        }

        // Construir el enlace completo con el ID modificado
        $modifiedId = implode('', $idCharacters);
        $modifiedLink = $serverPrefix . $modifiedId;
        return $modifiedLink;
    }

}
