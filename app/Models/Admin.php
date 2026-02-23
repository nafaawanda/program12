<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;
    protected $table = 'admins';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nama', 'alamat', 'no_telp', 'email', 'password', 'role'
    ];
    protected $hidden = ['password'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($admin) {
            if (!$admin->id) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id, 3)) + 1 : 1;
                $admin->id = 'ADM' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
