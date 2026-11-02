<?php

namespace Zzktz\LaravelKingbase\Tests\Unit;

use Illuminate\Database\PostgresConnection;
use PHPUnit\Framework\TestCase;
use Zzktz\LaravelKingbase\KingbaseConnection;

class KingbaseConnectionTest extends TestCase
{
    public function testConnectionUsesPostgresCompatibleLaravelConnection()
    {
        $this->assertTrue(is_subclass_of(KingbaseConnection::class, PostgresConnection::class));
    }
}
