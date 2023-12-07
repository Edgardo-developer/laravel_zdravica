<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrepareEntityController extends Controller
{
    // fields of Contact in the AmoCRM
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

    // fields of the lead in the AmoCRM
    private $mergedLeadFields = [

    ];

    /**
     * @param array $clientDB
     * @return array
     * Description: prepares the array for the contact
     */
    public function prepareContact(array $clientDB) : array{

    }

    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepareLead(array $leadDB, int $contactId) : array{

    }
}
