<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusUser extends Model
{
    public $timestamps = false;

    protected $table = 'status_user';

    protected $fillable = ['kode', 'label'];

    // ── Relationships ──────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class, 'status_id');
    }
}
