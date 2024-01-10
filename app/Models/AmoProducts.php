<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmoProducts extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv1';
    public $timestamps = false;
    protected $table = 'amocrm_products';
    protected $fillable = ['amoID','DBId','name'];
}
