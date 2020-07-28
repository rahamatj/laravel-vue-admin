<?php

namespace App\Repositories;

use App\Datatable\Datatable;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function datatable()
    {
        $query = Client::join('users', 'clients.user_id', '=', 'users.id')
                        ->select([
                            'clients.id',
                            'users.name as user_name',
                            'clients.client',
                            'clients.platform',
                            'clients.ip',
                            'clients.is_enabled',
                            'clients.logged_in_at',
                            'clients.created_at'
                        ]);

        $datatable = new Datatable($query);

        $datatable->latest();
        $datatable->filterColumns([
            'users.name',
            'clients.client',
            'clients.platform',
            'clients.ip',
            'clients.logged_in_at',
            'clients.created_at'
        ]);

        return $datatable->get();
    }

    public function changeEnabledStatus(Client $client)
    {
        $client->is_enabled = !$client->is_enabled;

        return $client->save();
    }

    public function delete(Client $client)
    {
        return $client->delete();
    }
}