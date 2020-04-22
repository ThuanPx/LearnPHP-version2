<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'content'
    ];

    /**
     * Get the image
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the comments
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
