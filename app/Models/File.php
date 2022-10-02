<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class File extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable=[
        "name",
        "path",
        "size",
        "user_id",
        "hashedfile"
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
    

    public function user() {
    return $this->belongsTo(User::class);
    }
}
