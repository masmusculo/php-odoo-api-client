<?php

declare(strict_types=1);

/*
 * This file is part of package ang3/php-odoo-api-client
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\Odoo;

use Ang3\Component\Odoo\Exception\ConnectionException;

class Connection
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string
     */
    private $scheme;

    public function __construct(
        string $host,
        string $username,
        string $password,
        string $database,
        string $scheme = 'https'
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->scheme = $scheme;
    }

    public function __toString(): string
    {
        return sprintf('%s://%s:%s@%s/%s', $this->scheme, $this->username, urlencode($this->password), $this->host, $this->database);
    }

    public static function create(array $config): self
    {
        $getParam = static function ($config, $paramName) {
            $value = $config[$paramName] ?? null;

            if (null === $value) {
                throw new ConnectionException(sprintf('Missing configuration parameter "%s".', $paramName));
            }

            return $value;
        };

        return new self(
            $getParam($config, 'host'),
            $getParam($config, 'username'),
            $getParam($config, 'password'),
            $getParam($config, 'database'),
            $config['scheme'] ?? 'https',
        );
    }

    /**
     * @throws ConnectionException on invalid DSN
     */
    public static function parseDsn(string $dsn): self
    {
        /** @var array|false $parsedUrl */
        $parsedUrl = parse_url($dsn);

        if (!\is_array($parsedUrl) && $parsedUrl) {
            throw ConnectionException::invalidDsn($dsn);
        }

        [$scheme, $host, $user, $password, $path] = [
            $parsedUrl['scheme'] ?? null,
            $parsedUrl['host'] ?? null,
            $parsedUrl['user'] ?? null,
            $parsedUrl['pass'] ?? null,
            $parsedUrl['path'] ?? null,
        ];

        if (!$scheme) {
            throw ConnectionException::invalidDsn($dsn, 'Missing scheme.');
        }

        if (!\in_array($scheme, ['http', 'https'], true)) {
            throw ConnectionException::invalidDsn($dsn, sprintf('The scheme "%s" is not supported (supported: "http" or "https").', $scheme));
        }

        if (!$host) {
            throw ConnectionException::invalidDsn($dsn, 'Missing host.');
        }

        if (!$user) {
            throw ConnectionException::invalidDsn($dsn, 'Missing username.');
        }

        if (!$password) {
            throw ConnectionException::invalidDsn($dsn, 'Missing user password.');
        }

        if (!$path) {
            throw ConnectionException::invalidDsn($dsn, 'Missing path.');
        }

        $database = substr($path, 0, 1) === '/' ? substr($path, 1) : $path;

        return new self($host, $user, urldecode($password), $database);
    }

    /**
     * Gets the unique name of this connection.
     */
    public function getIdentifier(): string
    {
        return sha1(sprintf('%s.%s.%s', $this->host, $this->database, $this->username));
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getUrl(): string
    {
        return sprintf('%s://%s', $this->scheme, $this->host);
    }
}
