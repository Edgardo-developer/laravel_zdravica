<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffersDB extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv2';
    protected $table = 'amocrm_products';
}
