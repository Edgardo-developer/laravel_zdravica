<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PATIENTS extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv2';
    protected $primaryKey = 'PATIENTS_ID';
    protected $table = 'master.dbo.PATIENTS';
}
