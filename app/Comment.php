<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'content', 'commentable_id', 'commentable_type'
    ];

    /**
     * Get the owning imageable model
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the image
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the comment
     */
    public function replies()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
