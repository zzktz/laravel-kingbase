<?php

namespace Zzktz\LaravelKingbase;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;
use PDO;

/**
 * PDO connector for the KingbaseES PHP driver (pdo_kdb).
 */
class KingbaseConnector extends Connector implements ConnectorInterface
{
    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    /**
     * Establish a database connection.
     *
     * @param array $config
     * @return PDO
     */
    public function connect(array $config)
    {
        $connection = $this->createConnection(
            $this->getDsn($config),
            $config,
            $this->getOptions($config)
        );

        $this->configureIsolationLevel($connection, $config);
        $this->configureEncoding($connection, $config);
        $this->configureTimezone($connection, $config);
        $this->configureSchema($connection, $config);
        $this->configureApplicationName($connection, $config);
        $this->configureSynchronousCommit($connection, $config);

        return $connection;
    }

    protected function configureIsolationLevel($connection, array $config)
    {
        if (isset($config['isolation_level'])) {
            $connection->prepare(
                "set session characteristics as transaction isolation level {$config['isolation_level']}"
            )->execute();
        }
    }

    protected function configureEncoding($connection, array $config)
    {
        if (isset($config['charset'])) {
            $connection->prepare("set names '{$config['charset']}'")->execute();
        }
    }

    protected function configureTimezone($connection, array $config)
    {
        if (isset($config['timezone'])) {
            $connection->prepare("set time zone '{$config['timezone']}'")->execute();
        }
    }

    protected function configureSchema($connection, array $config)
    {
        if (isset($config['schema'])) {
            $connection->prepare('set search_path to ' . $this->formatSchema($config['schema']))->execute();
        }
    }

    protected function formatSchema($schema)
    {
        if (is_array($schema)) {
            return '"' . implode('", "', $schema) . '"';
        }

        return '"' . $schema . '"';
    }

    protected function configureApplicationName($connection, array $config)
    {
        if (isset($config['application_name'])) {
            $connection->prepare("set application_name to '{$config['application_name']}'")->execute();
        }
    }

    protected function configureSynchronousCommit($connection, array $config)
    {
        if (isset($config['synchronous_commit'])) {
            $connection->prepare("set synchronous_commit to '{$config['synchronous_commit']}'")->execute();
        }
    }

    /**
     * Build the Kingbase PDO DSN.
     *
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        $host = isset($config['host']) ? 'host=' . $config['host'] . ';' : '';
        $dsn = "kdb:{$host}dbname='" . ($config['database'] ?? '') . "'";

        if (isset($config['port'])) {
            $dsn .= ';port=' . $config['port'];
        }

        foreach (['sslmode', 'sslcert', 'sslkey', 'sslrootcert'] as $option) {
            if (isset($config[$option])) {
                $dsn .= ";{$option}={$config[$option]}";
            }
        }

        return $dsn;
    }
}
