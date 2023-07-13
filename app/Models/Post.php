<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasUuids;
    protected $table = 'posts';
    protected $primaryKey  = "post_id";
    public $incrementing = false;
    protected $fillable = ["title" , "description" , "comments"];
    protected $casts = [
        'comments' => 'array',
    ];

}
