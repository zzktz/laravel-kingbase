<?php

namespace Zzktz\LaravelKingbase\Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use PHPUnit\Framework\TestCase;
use Zzktz\LaravelKingbase\KingbaseConnector;
use Zzktz\LaravelKingbase\KingbaseServiceProvider;

class KingbaseConnectorTest extends TestCase
{
    public function testDsnUsesTheKingbaseKdbScheme()
    {
        $connector = new class extends KingbaseConnector {
            public function dsn(array $config)
            {
                return $this->getDsn($config);
            }
        };

        $this->assertSame(
            "kdb:host=127.0.0.1;dbname='doc';port=54321;sslmode=prefer",
            $connector->dsn([
                'host' => '127.0.0.1',
                'database' => 'doc',
                'port' => '54321',
                'sslmode' => 'prefer',
            ])
        );
    }

    public function testServiceProviderRegistersTheKingbaseDriver()
    {
        $container = new Container;
        $provider = new KingbaseServiceProvider($container);
        $provider->register();

        $this->assertTrue($container->bound('db.connector.kingbase'));
        $this->assertInstanceOf(KingbaseConnector::class, $container->make('db.connector.kingbase'));
        $this->assertNotNull(Connection::getResolver('kingbase'));
    }
}
