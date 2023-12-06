<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrepareEntityController extends Controller
{
    private $mergedContactFields = [
        'name',
        'first_name',
        'last_name',
        'responsible_user_id',
        'created_by',
        'updated_by',
        'custom_fields_values'  => [
            170783 => 'mobile',
            170785 => 'email',
            391181 => 'FIO',
            391183 => 'Birthday',
            391185 => 'POL',
        ]
    ];
    private $mergedLeadFields = [

    ];

    public function prepareContact(array $clientDB){

    }

    public function prepareLead(array $leadDB) : array{

    }
}
