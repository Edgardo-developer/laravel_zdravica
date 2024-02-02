<?php
namespace App\Http\Controllers\Contacts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ContactsGeneralController extends Controller
{
    private ContactsBuilderController $ContactsBuilderController;
    private ContactsPrepareController $ContactsPrepareController;
    private ContactsPresendController $ContactsPresendController;
    private ContactsRequestController $ContactsRequestController;
    private $client;

    public function __construct($client){
        $this->ContactsBuilderController = new ContactsBuilderController();
        $this->ContactsPrepareController = new ContactsPrepareController();
        $this->ContactsPresendController = new ContactsPresendController($client);
        $this->ContactsRequestController = new ContactsRequestController();
        $this->client = $client;
    }

    public function getRow(int $id, bool $declareCall = false) : array{
        return $this->ContactsBuilderController->getRow($id,$declareCall);
    }

    public function prepare(array $contactDB, int $contactID = 0) : array{
        return $this->ContactsPrepareController->prepare($contactDB,$contactID);
    }

    public function getAmoID(array $contactDB) : int{
        $contactID = $this->AccrossGetRequests($contactDB);
        if ($contactID) {
            return $contactID;
        }

        $prepared = $this->prepare($contactDB);
        $contactResponse = $this->create($prepared);
        try {
            $result = json_decode($contactResponse->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
            if ($result['_embedded'] && $result['_embedded']['contacts']){
                return $result['_embedded']['contacts'][0]['id'];
            }
        }catch (\JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getFile());
            Log::warning($ex->getLine());
        }
        return 0;
    }

    public function AccrossGetRequests(array $contactDB) : int{
        $contacts = $this->ContactsPresendController->checkExistsByNumber($contactDB);
        if (!$contacts){
            if (isset($contactDB['EMAIL'])) {
                $contacts = $this->ContactsPresendController->checkExistsByEMAIL($contactDB['EMAIL']);
            }
            if (!$contacts && isset($contactDB['FIO'])) {
                $contacts = $this->ContactsPresendController->checkExistsByFIO($contactDB['FIO']);
            }
        }
        if ($contacts){
            return $this->ContactsPresendController->checkExists($contactDB, $contacts);
        }
        return 0;
    }

    public function create(array $prepared){
        return $this->ContactsRequestController->create($this->client, $prepared);
    }

    public function update(array $preparedData = null)
    {
        return $this->ContactsRequestController->update($this->client,$preparedData);
    }

    public function get(string $query): array
    {
        return $this->ContactsRequestController->get($this->client,$query);
    }
}
