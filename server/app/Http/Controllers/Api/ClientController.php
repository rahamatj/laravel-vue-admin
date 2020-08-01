<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\Client as ClientResource;

class ClientController extends Controller
{
    public function index(ClientRepositoryInterface $clientRepository)
    {
        return ClientResource::collection($clientRepository->datatable());
    }

    public function changeEnabledStatus(Client $client, ClientRepositoryInterface $clientRepository)
    {
        $clientRepository->changeEnabledStatus($client);

        return response()->json([
           'message' => 'Enabled status changed successfully!'
        ]);
    }

    public function destroy(Client $client, ClientRepositoryInterface $clientRepository)
    {
        $clientRepository->delete($client);

        return response()->json([
           'message' => 'Client deleted successfully!'
        ]);
    }
}
