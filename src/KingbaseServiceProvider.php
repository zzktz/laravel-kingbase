<?php

namespace Zzktz\LaravelKingbase;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

/**
 * Registers the KingbaseES connection without modifying Laravel framework files.
 */
class KingbaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('db.connector.kingbase', KingbaseConnector::class);

        Connection::resolverFor('kingbase', function ($connection, $database, $prefix, $config) {
            return new KingbaseConnection($connection, $database, $prefix, $config);
        });
    }
}
