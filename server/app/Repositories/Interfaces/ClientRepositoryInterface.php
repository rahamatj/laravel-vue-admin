<?php

namespace App\Repositories\Interfaces;

use App\Client;

interface ClientRepositoryInterface
{
    public function datatable();

    public function changeEnabledStatus(Client $client);

    public function delete(Client $client);
}