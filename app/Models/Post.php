<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'post',
    ];

    public static function createPost($user_id, $post)
    {
        $result = Post::insert([
            'user_id' => $user_id,
            'post' => json_encode($post),
            'created_at' => now(),
            'updated_at' => null // Exclude updating the timestamp
        ]);
        return $result;
    }
}
