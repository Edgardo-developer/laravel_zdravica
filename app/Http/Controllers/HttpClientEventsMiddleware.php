<?php
namespace App\Http\Controllers;

use GuzzleHttp\Promise;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;

class HttpClientEventsMiddleware {

    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {

            $laravelRequest = new Request($request);

            event(new RequestSending($laravelRequest));

            return $handler($request, $options)->then(
                $this->onSuccess($laravelRequest),
                $this->onFailure($laravelRequest)
            );
        };
    }

    protected function onSuccess(Request $request)
    {
        return function ($response) use ($request) {

            event(new ResponseReceived($request, new Response($response)));

            return $response;
        };
    }

    protected function onFailure(Request $request)
    {
        return function ($reason) use ($request) {

            event(new ConnectionFailed($request));

            return Promise\Create::rejectionFor($reason);
        };
    }


}
