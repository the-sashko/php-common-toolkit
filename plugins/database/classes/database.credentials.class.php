<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCredentials;

use Core\Plugins\Database\Exceptions\DatabaseCredentialsException;

/**
 * Plugin For Working With Data Base
 */
class DatabaseCredentials implements IDataBaseCredentials
{
    const DEFAULT_HOST = 'localhost';

    const DEFAULT_PORT = 5433;

    const DEFAULT_CACHE_TYPE = 'mock';

    private $_dsn = null;

    private $_user = null;

    private $_password = null;

    private $_cacheType = null;

    public function __construct(?array $configData = null)
    {
        if (empty($configData)) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_CONFIG_DATA_IS_EMPTY,
                $exceptionClass::CODE_CREDENTIALS_CONFIG_DATA_IS_EMPTY
            );
        }

        $this->_setDsnFromConfig($configData);
        $this->_setUserFromConfig($configData);
        $this->_setPasswordFromConfig($configData);
        $this->_setCacheTypeFromConfig($configData);
    }

    public function getDsn(): string
    {
        if (empty($this->_dsn)) {
            throw new DatabaseCredentialsException(
                DatabaseCredentialsException::MESSAGE_CREDENTIALS_DSN_IS_EMPTY,
                DatabaseCredentialsException::CODE_CREDENTIALS_DSN_IS_EMPTY
            );
        }

        return $this->_dsn;
    }

    public function getUser(): ?string
    {
        return $this->_user;
    }

    public function getPassword(): ?string
    {
        return $this->_password;
    }

    public function getCacheType(): ?string
    {
        return $this->_cacheType;
    }

    private function _getTypeFromConfig(array $configData): string
    {
        if (
            !array_key_exists('type', $configData) ||
            empty($configData['type'])
        ) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_DB_TYPE_IS_NOT_SET,
                $exceptionClass::CODE_CREDENTIALS_DB_TYPE_IS_NOT_SET
            );
        }

        return (string) $configData['type'];
    }

    private function _getDataBaseNameFromConfig(array $configData): string
    {
        if (
            !array_key_exists('db', $configData) ||
            empty($configData['db'])
        ) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_DB_NAME_IS_NOT_SET,
                $exceptionClass::CODE_CREDENTIALS_DB_NAME_IS_NOT_SET
            );
        }

        return (string) $configData['db'];
    }

    private function _getHostFromConfig(array $configData): string
    {
        if (
            !array_key_exists('host', $configData) ||
            empty($configData['host'])
        ) {
            return static::DEFAULT_HOST;
        }

        return (string) $configData['host'];
    }

    private function _getPortFromConfig(array $configData): int
    {
        if (
            !array_key_exists('port', $configData) ||
            empty($configData['port'])
        ) {
            return static::DEFAULT_PORT;
        }

        return (int) $configData['port'];
    }

    private function _setUserFromConfig(array $configData): void
    {
        if (
            array_key_exists('user', $configData) &&
            !empty($configData['user'])
        ) {
            $this->_user = (string) $configData['user'];
        }
    }

    private function _setPasswordFromConfig(array $configData): void
    {
        if (
            array_key_exists('password', $configData) &&
            !empty($configData['password'])
        ) {
            $this->_password = (string) $configData['password'];
        }
    }

    private function _setCacheTypeFromConfig(array $configData): void
    {
        if (
            !array_key_exists('cache_type', $configData) ||
            empty($configData['cache_type'])
        ) {
            $cacheType = static::DEFAULT_CACHE_TYPE;
        }

        $cacheType = (string) $configData['cache_type'];

        $this->_cacheType = $cacheType;
    }

    private function _setDsnFromConfig(array $configData): void
    {
        $type         = $this->_getTypeFromConfig($configData);
        $databaseName = $this->_getDataBaseNameFromConfig($configData);
        $host         = $this->_getHostFromConfig($configData);
        $port         = $this->_getPortFromConfig($configData);

        $dsn = '%s:host=%s;port=%d;dbname=%s';

        $this->_dsn = sprintf($dsn, $type, $host, $port, $databaseName);
    }
}
