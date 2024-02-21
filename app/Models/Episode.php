<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Episode extends Model
{
    use HasFactory;

    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }

    public function releases()
    {
        try {
            $data = $this->select('animes.name', 'animes.slug','animes.banner', 'animes.poster', 'players.created_at', 'episodes.number', 'players.languaje')
            ->leftJoin('players','players.episode_id','episodes.id')
            ->leftJoin('animes', 'animes.id', 'episodes.anime_id')
            ->where('animes.status', 1)
            ->groupBy('players.languaje', 'episodes.id')
		    ->orderBy('players.id', 'desc')
            ->limit(30)
			->get();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function episode($request)
    {
        try {
            $anime = Anime::select('id','name','slug','banner','poster')->where('slug', $request->slug)->first();
            $data = $this
            ->select('id','number','views')
		    ->where('anime_id',$anime->id)
			->where('number',$request->number)
			->first();
            $data->anime = $anime;
            $data->anterior = $this->previous($anime, $request->number);
            $data->siguiente = $this->next($anime, $request->number);
            $data->players = $this->players($data->id);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function players($id)
    {
        return Player::select('players.id','languaje','server_id')
            ->leftJoin('servers','servers.id','=','players.server_id')
            ->where('episode_id',$id)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere('status', 2);
            })
            ->with(['server'])
            ->get()
            ->groupby('languaje');
    }
	
	
	public function previous($anime, $number)
    {
        return $this->select('number')
			->where('anime_id',$anime->id)
			->where('number',$number - 1)
			->first();
    }	

	public function next($anime, $number)
    {
        return $this
            ->select('number')
			->where('anime_id',$anime->id)
			->where('number',$number + 1)
			->first();
    }

    
}
