<?php

declare(strict_types=1);

namespace PoPSchema\CustomPostMedia\FieldResolvers;

use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoPSchema\CustomPosts\FieldInterfaceResolvers\IsCustomPostFieldInterfaceResolver;
use PoP\ComponentModel\FieldResolvers\FieldSchemaDefinitionResolverInterface;
use PoPSchema\CustomPostMedia\FieldInterfaceResolvers\SupportingFeaturedImageFieldInterfaceResolver;

class CustomPostFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return [
            IsCustomPostFieldInterfaceResolver::class,
        ];
    }

    public static function getImplementedInterfaceClasses(): array
    {
        return [
            SupportingFeaturedImageFieldInterfaceResolver::class,
        ];
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'hasFeaturedImage',
            'featuredImage',
        ];
    }

    /**
     * By returning `null`, the schema definition comes from the interface
     *
     * @return void
     */
    public function getSchemaDefinitionResolver(TypeResolverInterface $typeResolver): ?FieldSchemaDefinitionResolverInterface
    {
        return null;
    }

    /**
     * @param array<string, mixed> $fieldArgs
     * @param array<string, mixed>|null $variables
     * @param array<string, mixed>|null $expressions
     * @param array<string, mixed> $options
     * @return mixed
     */
    public function resolveValue(
        TypeResolverInterface $typeResolver,
        object $resultItem,
        string $fieldName,
        array $fieldArgs = [],
        ?array $variables = null,
        ?array $expressions = null,
        array $options = []
    ) {
        $cmsmediapostsapi = \PoPSchema\Media\PostsFunctionAPIFactory::getInstance();
        $post = $resultItem;
        switch ($fieldName) {
            case 'hasFeaturedImage':
                return $cmsmediapostsapi->hasCustomPostThumbnail($typeResolver->getID($post));

            case 'featuredImage':
                return $cmsmediapostsapi->getCustomPostThumbnailID($typeResolver->getID($post));
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'featuredImage':
                $instanceManager = InstanceManagerFacade::getInstance();
                $fieldInterfaceResolver = $instanceManager->getInstance(SupportingFeaturedImageFieldInterfaceResolver::class);
                return $fieldInterfaceResolver->getFieldTypeResolverClass($fieldName, $fieldArgs);
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
