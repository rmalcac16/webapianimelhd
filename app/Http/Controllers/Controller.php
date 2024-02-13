<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\Anime;
use App\Models\Episode;

class Controller extends BaseController
{
    protected $anime, $episode;

    public function __construct(Anime $anime, Episode $episode)
    {
        $this->anime = $anime;
        $this->episode = $episode;
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

}
