<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackList extends Model
{
    protected $fillable =
        [
            'user_id',
            'track_code',
            'to_china',
            'to_customs',
            'reg_china',
            'reg_almaty',
            'reg_client',
            'detail',
            'status',
            'city'
        ];
    protected $hidden =
        [
            'created_at',
            'updated_at'
        ];
    public function user()
    {
        return $this->hasOneThrough(User::class, ClientTrackList::class, 'track_code', 'id', 'track_code', 'user_id');
    }
}
