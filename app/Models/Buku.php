<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    protected $table = 'bukus';
    protected $fillable = [
        'kode_buku', 'nama_buku', 'nama_penulis', 'penerbit', 'th_terbit', 'stock', 'jenis_buku', 'kode_rak', 'isbn', 'id_penulis', 'id_penerbit', 'kategori'
    ];
}
