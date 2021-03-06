<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option;
use App\Util\LoaderQuery;

class Poll extends Model
{
    protected $fillable = ['question', 'captcha', 'multiple_choice', 'block_by_ip', 'block_by_cookie'];
    protected $appends = ['options', 'poll_url', 'total_votes'];
    protected $hidden = ['owner_id', 'password'];

    public function getOptionsAttribute()
    {
        return Option::where('poll_id', '=', $this->id)->get();
    }

    public function getPollUrlAttribute()
    {
        $id = base64_encode($this->id);
        $endpoint = "/poll/$id";
        if (env('APP_ENV') === 'local')
        {
            return url($endpoint);
        }
        else
        {
            return secure_url($endpoint);
        }
    }

    public function getTotalVotesAttribute()
    {
        return LoaderQuery::shouldLoadVotes() ? Vote::where('poll_id', '=', $this->id)->count() : null;
    }
}
