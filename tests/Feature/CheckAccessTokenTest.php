<?php

namespace Tests\Feature;

use App\Http\Controllers\RequestController;
use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckAccessTokenTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        AmoCrmTable::create([
           'key'=>'refresh_token',
           'value'=>'def502000d472d2d338891ab03ad960fb38a3f768b1c0d765949ea75d4fcf55a5cf6951143ca12059b0a92615db28ea78b2a3e67761d1960b8b2b9a6e1971bc93da047f286e606191be31ad2a6de1d28d7ff8a2c5ebde589da6f1453c7de37ced1f72f89e1d9f290898cd0ffdffe0e1d4aaa43b6b84196e2b3a495f893c8877b4483336ebf79530a70d4e31b93cb8c38f7ea755150c3ab80752db3348fefc56089bc47e075be113fd6aa69ce972fca25a9a15c19b5ba3b2703d206f71df14708aeb9358f55ee5d082be03f4dcc04527b01efdbd876dfc6e79bd347f402ad766503c6ed786fa59baffc1e94f955d42da81affc14f52e205ece6ede9865d6bbd82d197e6bdb5af850557f379312c0abd5de509a00d89df29fb0df80b2a420146cf27e206a2b77ebc0f0baeac958e3fcf02df7874468d4571461b42b803046a347153d0bc73b40a97eb5683279d4c6025461bc85ba8cb4105ce57c229771562476cdcb18ffeeecc62145c0ec5cef2bdec73cd72c954439e422f1011d996a2f995a06d4ea79eff2f7b93945fb31f67a90823afecd4862b92829fdfe93c78600b4bc276a8d20407c77aedc477e8b6fad6bb63f6b359cd57ce91fb55f8369466a57ee03fa89f4d30fc7f92709b6375cee580f123168d56050fdb8c6341daabcc1a15d688b600ee2fdbeab81f25a6087a48',
        ]);
        $RequestController = new RequestController();
        $client = new Client(['verify'=>false]);
        $request = $RequestController::updateAccess($client);
        $this->assertEquals(200, $request->getStatusCode());
    }
}
