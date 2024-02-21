<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Player extends Model
{
    use HasFactory;

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function getPlayerById($id){
        return $this->select('players.id','code','embed','title','languaje','server_id')
            ->join('servers','servers.id','=','players.server_id')
            ->where('players.id',$id)
            ->orderBy('players.server_id','asc')
            ->first();
    }

    
}
