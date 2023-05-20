<?php

namespace Nerd4ever\OidcServerBundle\Persistence\Mapping;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use League\Bundle\OAuth2ServerBundle\Model\RefreshToken;
use Nerd4ever\OidcServerBundle\Entity\AbstractSessionEntity;
use Nerd4ever\OidcServerBundle\Entity\Session;

/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
class Driver implements MappingDriver
{
    private string $sessionClass;
    private ?string $tablePrefix;

    public function __construct(string $sessionClass, ?string $tablePrefix = 'oauth2_')
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
            case Session::class:
                $this->buildSessionEntityMetadata($metadata);
                break;
            default:
                throw new \RuntimeException(sprintf('%s cannot load metadata for class %s', __CLASS__, $className));
        }
    }

    public function getAllClassNames(): array
    {
        $data = array_merge(
            [
                AbstractSessionEntity::class,
            ],
            Session::class === $this->sessionClass ? [Session::class] : [],
        );
        return $data;
    }

    public function isTransient(string $className): bool
    {
        return false;
    }

    private function buildSessionEntityMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setTable($this->tablePrefix . 'session')
            ->createField('identifier', 'string')->makePrimaryKey()->length(80)->build();
    }

    private function buildAbstractSessionMetadata(ClassMetadata $metadata): void
    {
        (new ClassMetadataBuilder($metadata))
            ->setMappedSuperClass()
            ->createField('userAgent', 'string')->length(2048)->nullable(true)->build()
            ->createField('userAddress', 'string')->columnName('user_address')->length(64)->nullable(true)->build()
            ->createField('revokedAt', 'datetimetz')->columnName('revoked_at')->nullable(true)->build()
            ->createField('createdAt', 'datetimetz')->columnName('created_at')->nullable(false)->build()
            ->createManyToOne('refreshToken', RefreshToken::class)->addJoinColumn('refresh_token', 'identifier', true, false, 'CASCADE')->build();

        $metadata->addLifecycleCallback('updateCreatedAt', 'prePersist');
        $metadata->addLifecycleCallback('updateCreatedAt', 'preUpdate');
    }

}