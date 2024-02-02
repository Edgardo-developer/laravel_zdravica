<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PATIENTS extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv1';
    protected $primaryKey = 'PATIENTS_ID';
    protected $fillable = [
        'NOM','PRENOM', 'PATRONYME', 'FIO', 'agePat', 'MOBIL_NYY'
    ];
    protected $table = 'master.dbo.PATIENTS';
}
