<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasFactory;
    protected $table = 'staff';
    protected $primaryKey = 'id_staff';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nama', 'alamat', 'no_telp', 'email', 'password', 'role'
    ];
    protected $hidden = ['password'];

    public function getAuthIdentifierName()
    {
        return 'id_staff';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($staff) {
            if (!$staff->id_staff) {
                $last = self::orderBy('id_staff', 'desc')->first();
                $num = $last ? intval(substr($last->id_staff, 3)) + 1 : 1;
                $staff->id_staff = 'STF' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
