<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class paypalcontroller extends Controller
{
    var $clientId = "AVArtEGDuj9QCCcjqgSSAadb_aHE0U-Dv0FL8bRCYT-VVU7aht-WlvVqoJ1deOCRidF6-PqFQcZBqe94";
    var $clientSecret = "EC3aaHsVG6OXVqmVrOC-0LRLoqUALemcX4Xt751OEwyw7If73xM567hqOJnnxRAA4HqtPVAWebJ8KphE";

    var $baseUrl = "https://api-m.sandbox.paypal.com";

    function createRequest()
    {
        $token = $this->createToken();
        $client = $this->createClient($token);
        $parameter = new \stdClass();
        $parameter->intent = "CAPTURE";
        $appContext = new \stdClass();
        $appContext->return_url = "https://example.com/return";
        $appContext->cancel_url = "https://example.com/return";
        $parameter->application_context = $appContext;
        $purchase = array();
        $purchaseItem = new \stdClass();
        $purchaseItem->items = array();
        $item = new \stdClass();
        $item->name = "Sambal";
        $item->description = "Super Sambal";
        $item->quantity = 3;
        $item->unit_amount = new \stdClass();
        $item->unit_amount->currency_code = "SGD";
        $item->unit_amount->value = 150;
        $purchaseItem->items[] = $item;
        $purchaseItem->amount = new \stdClass();
        $purchaseItem->amount->currency_code = "SGD";
        $purchaseItem->amount->value = 450;
        $purchaseItem->amount->breakdown = new \stdClass();
        $purchaseItem->amount->breakdown->item_total = new \stdClass();
        $purchaseItem->amount->breakdown->item_total->currency_code = "SGD";
        $purchaseItem->amount->breakdown->item_total->value = 450;
        $parameter->purchase_units = array();
        $parameter->purchase_units[] = $purchaseItem;

        $jsonBody = json_encode($parameter);
        $response = $client->POST('/v2/checkout/orders', [
            'body' => $jsonBody
        ]);
        $content = $response->getBody()->getContents();
        $data = \GuzzleHttp\json_decode($content);
        $link = $data->links[1]->href;
        $id = $data->id;
        dd($link . ' - ' . $id);
    }

    function createClient($token)
    {
        return new Client(['base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ]]);
    }

    function createTokenClient()
    {
        return new Client(['base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ":" . $this->clientSecret),

            ]]);
    }

    function createToken()
    {
        $client = $this->createTokenClient();
        $response = $client->POST('/v1/oauth2/token', [
            'form_params' => ['grant_type' => 'client_credentials']
        ]);
        $content = $response->getBody()->getContents();
        $data = \GuzzleHttp\json_decode($content);
        return $data->access_token;
    }
}
