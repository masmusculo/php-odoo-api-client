<?php

declare(strict_types=1);

/*
 * This file is part of package ang3/php-odoo-api-client
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Ang3\Component\Odoo\Metadata;

class Version
{
    /**
     * @var int
     */
    private $majorVersion;

    /**
     * @var int
     */
    private $minorVersion;

    /**
     * @var int
     */
    private $patchVersion;

    /**
     * @var string
     */
    private $buildName;

    /**
     * @var string
     */
    private $buildIdentifier;

    /**
     * @var string
     */
    private $buildVersion;

    /**
     * @var int
     */
    private $protocolVersion;

    public function __construct(
        int $majorVersion,
        int $minorVersion,
        int $patchVersion,
        string $buildName,
        string $buildIdentifier,
        string $buildVersion,
        int $protocolVersion
    ) {
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->patchVersion = $patchVersion;
        $this->buildName = $buildName;
        $this->buildIdentifier = $buildIdentifier;
        $this->buildVersion = $buildVersion;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Creates the instance from Odoo response payload.
     */
    public static function create(array $payload): self
    {
        $infos = $payload['server_version_info'];

        return new self($infos[0], $infos[1], $infos[2], (string) $infos[3], (string) $infos[4], (string) $infos[5], $payload['protocol_version']);
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return sprintf('%s.%s.%s+%s', $this->majorVersion, $this->minorVersion, $this->patchVersion, $this->buildVersion);
    }

    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    public function getBuildName(): string
    {
        return $this->buildName;
    }

    public function getBuildIdentifier(): string
    {
        return $this->buildIdentifier;
    }

    public function getBuildVersion(): string
    {
        return $this->buildVersion;
    }

    public function getProtocolVersion(): int
    {
        return $this->protocolVersion;
    }
}
