<?php

namespace Nerd4ever\OidcServerBundle\Persistence\Mapping;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Nerd4ever\OidcServerBundle\Entity\SessionEntity;
use Nerd4ever\OidcServerBundle\Model\AbstractSessionEntity;

/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
class Driver implements MappingDriver
{
    private string $sessionClass;
    private string $tablePrefix;

    public function __construct(string $sessionClass, string $tablePrefix = 'oauth2_')
    {
        $this->sessionClass = $sessionClass;
        $this->tablePrefix = $tablePrefix;
    }

    public function loadMetadataForClass(string $className, ClassMetadata $metadata)
    {
        switch ($className) {
            case AbstractSessionEntity::class:
                $this->buildAbstractSessionMetadata($metadata);
                break;
            case SessionEntity::class:
                $this->buildSessionEntityMetadata($metadata);
                break;
            default:
                throw new \RuntimeException(sprintf('%s cannot load metadata for class %s', __CLASS__, $className));
        }
    }

    public function getAllClassNames(): array
    {
        return array_merge(
            [
                AbstractSessionEntity::class,
            ],
            SessionEntity::class === $this->sessionClass ? [SessionEntity::class] : [],
        );
    }

    public function isTransient(string $className): bool
    {
        return false;
    }

    private function buildSessionEntityMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setTable($this->tablePrefix . 'session')
            ->createField('identifier', 'string')->makePrimaryKey()->length(32)->build();
    }

    private function buildAbstractSessionMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setMappedSuperClass()
            ->createField('userAgent', 'string')->length(2048)->nullable(true)->build()
            ->createField('clientAddress', 'string')->length(64)->nullable(true)->build()
            ->createField('userIdentifier', 'string')->length(32)->nullable(false)->build()
            ->createField('refreshTokenIdentifier', 'string')->length(32)->nullable(false)->build()
            ->createField('accessTokenIdentifier', 'string')->length(32)->nullable(true)->build()
            ->createField('revokedAt', 'datetimetz')->nullable(true)->build();
    }
}