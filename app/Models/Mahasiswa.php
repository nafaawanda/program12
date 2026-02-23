<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mahasiswa extends Authenticatable
{
    use HasFactory;
    protected $table = 'mahasiswas';
    protected $fillable = [
        'nim', 'nama', 'tempat_lahir', 'tgl_lahir', 'prodi_id', 'th_masuk', 'email', 'password'
    ];
    protected $hidden = ['password'];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
