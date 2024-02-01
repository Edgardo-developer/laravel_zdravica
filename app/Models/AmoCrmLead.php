<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmoCrmLead extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $primaryKey = 'id';
    public $fillable = [
        'direction',
        'specDoc'  ,
        'patID',
        'fioDoc'  ,
        'billID'  ,
        'billSum',
        'offers'   ,
        'managerName'   ,
        'amoManagerID' ,
        'declareCall' ,
        'filial' ,
        'date' ,
        'created_at',
        'updated_at',
        'leadDBId'
    ];
    protected $connection = 'sqlsrv1';
    protected $table = 'master.dbo.amocrm_lead';
}
