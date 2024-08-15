<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scoring extends Model
{
    use HasFactory;
    protected $table = 'scoring';
    protected $fillable = [
        'nama',
        'scores',
        'sistem',
        'comment',
        'revisi',
        'template',
        'assessor',
        'tanggal',
        'status',
      ];
}
