<?php

namespace App\Models;

use App\Events\ModelCreated;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Post;

class Comment extends Model
{
    use NodeTrait, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'post_id',
        'user_id',
    ];

    protected $dispatchesEvents = [
        'created' => ModelCreated::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function comments(Post $post)
    {
        $comments = $post->validComments()
            ->withDepth()
            ->latest()
            ->get()
            ->toTree();
        return [
            'html' => view('front/comments', compact('comments'))->render(),
        ];
    }
}
