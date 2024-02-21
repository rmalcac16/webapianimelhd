<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

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


    public function video(Request $request)
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        $parse = parse_url($referer);
        if($parse['host'] != 'www.animelatinohd.com')
            return abort(403, 'Sin acceso');
        $id = Crypt::decryptString($request->id);
        $player = $this->player->getPlayerById($id);
        if(!$player)
            return abort(404, 'No encontrado');            
        $link = $this->getFullUrl($player);
        return redirect($link);   
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

}
