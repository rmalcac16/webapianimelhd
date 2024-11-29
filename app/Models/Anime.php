<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Anime extends Model
{
    use HasFactory;

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function releases(){
        try {
            return $this->select([
                    'animes.name',
                    'animes.slug',
                    'animes.poster',
                    \DB::raw("sum(episodes.views_app) as totalviews")
                ])
                ->leftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
                ->whereNotNull('episodes.id')
                ->groupBy('animes.id')
                ->orderBy('animes.id', 'desc')
                ->limit(14)
                ->get();
        } catch (Exception $e) {
            return [];
        }
    }

    public function latino(){
        try {
            $data = $this->select('name', 'slug', 'poster', 'vote_average','status',
		     DB::raw('MAX(number) as number'),DB::raw('MAX(players.id) as idplayer'))
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('players.languaje', '=', 1)
			->groupBy('animes.id')
			->orderBy('idplayer','desc')
			->get();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return array('message' => $e->getMessage());
        }
    }

    public function castellano(){
        try {
            $data = $this->select('name', 'slug', 'poster', 'vote_average','status',
		     DB::raw('MAX(number) as number'),DB::raw('MAX(players.id) as idplayer'))
			->LeftJoin('episodes', 'episodes.anime_id', '=', 'animes.id')
			->LeftJoin('players','episode_id', '=', 'episodes.id')
			->where('players.languaje', '=', 2)
			->groupBy('animes.id')
			->orderBy('idplayer','desc')
			->get();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return array('message' => $e->getMessage());
        }
    }

    public function trending()
    {
        try {
            $data = $this->select('name', 'slug', 'poster', 'vote_average', 'aired')
                ->join('episodes', 'episodes.anime_id', '=', 'animes.id')
                ->groupBy('animes.id')
                ->orderBy('vote_average', 'desc')
                ->limit(28)
                ->get();
            return response()->json(["popular_today" => $data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function moreview()
    {
        try {
            $data = $this->select(['name', 'slug', 'poster', 'aired', 'animes.views as totalviews'])
                ->join('episodes', 'episodes.anime_id', '=', 'animes.id')
                ->groupBy('animes.id')
                ->orderBy('totalviews', 'desc')
                ->limit(14)
                ->get();
            return response()->json(["being_watched" => $data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function search($request)
    {
        try {
            $data = $this->select('name', 'slug', 'type', 'poster')
                ->join('episodes', 'episodes.anime_id', '=', 'animes.id')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('name_alternative', 'LIKE', "%{$request->search}%")
                        ->orWhere('overview', 'LIKE', "%{$request->search}%")
                        ->orWhere('genres', 'LIKE', "%{$request->search}%")
                        ->orWhere('aired', 'LIKE', "%{$request->search}%");
                })
                ->groupBy('animes.id')
                ->orderBy('aired', 'desc')
                ->limit(10)
                ->get();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function anime($request){
        try {
            $data = $this
            ->select([
                app('db')->raw('animes.*, IFNULL(sum(episodes.views),0) as totalviews')
            ])
            ->with(['episodes' => function ($q) {
                $q->orderBy('number', 'desc')
                  ->select('id','anime_id','number','views','created_at');
            }])
            ->leftJoin('episodes','episodes.anime_id','=','animes.id')
            ->where('slug', $request->slug)
            ->groupBy('animes.id')
            ->first();
            $data->increment('views');
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function simulcast()
    {
        try {
            $data = $this->where('status',1)
			->select('name','slug','banner','broadcast',
				DB::raw('(select created_at from episodes where anime_id = animes.id order by number desc limit 1) as date'),
				DB::raw('(select HOUR(created_at) from episodes where anime_id = animes.id order by number desc limit 1) as hour'),
				DB::raw('(select number from episodes where anime_id = animes.id order by number desc limit 1) as lastEpisode'))
			->orderBy('hour','asc')
			->get()
			->groupBy('broadcast');
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

	public function animes($request)
    {
        try {
            $data = $this->select('animes.name', 'animes.slug', 'animes.poster', 'animes.aired', 'animes.vote_average')
                ->join('episodes', 'episodes.anime_id', '=', 'animes.id')
                ->groupBy('animes.id')
                ->orderBy('animes.id', 'desc');

            if ($request->type) {
                $data = $data->where('animes.type', $request->type);
            }
            if (isset($request->status)) {
                $data = $data->where('animes.status', $request->status);
            }
            if ($request->year) {
                $data = $data->whereYear('animes.aired', $request->year);
            }
            if ($request->genre) {
                $data = $data->where('animes.genres', 'LIKE', "%{$request->genre}%");
            }
            if ($request->search) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('animes.name', 'LIKE', "%{$request->search}%")
                        ->orWhere('animes.name_alternative', 'LIKE', "%{$request->search}%");
                });
            }

            $data = $data->simplePaginate(28);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

}
