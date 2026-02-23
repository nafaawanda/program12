<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class mahasiswas extends Authenticatable
{
    use HasFactory;

    protected $table = 'mahasiswas';
    protected $fillable = [
        'nim', 'nama', 'password', 'status', // menyesuaikan table mahasiswas
    ];
    protected $hidden = ['password'];
}
