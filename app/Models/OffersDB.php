<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffersDB extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv2';
    public $timestamps = false;
    protected $table = 'amocrm_products';
    protected $fillable = ['name','DBId','amoID'];
}
