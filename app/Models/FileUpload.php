<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileUpload extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'file_upload_url',
    ];

    public static function createFileUpload($user_id, $filepath)
    {
        FileUpload::insert([
            'user_id' => $user_id,
            'file_upload_url' => $filepath,
            'created_at' => now(),
            'updated_at' => null // Exclude updating the timestamp
        ]);
    }
}
