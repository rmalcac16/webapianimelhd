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

    
}
