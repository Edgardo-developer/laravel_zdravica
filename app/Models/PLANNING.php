<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PLANNING extends Model
{
    use HasFactory;
    protected $primaryKey = 'PLANNING_ID';
    public $timestamps = false;
    protected $table = 'master.dbo.PLANNING';
    protected $fillable = [
        'NOM',
        'PRENOM',
        'PATRONYME'
    ];
}
