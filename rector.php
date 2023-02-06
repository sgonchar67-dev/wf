<?php

declare(strict_types=1);

use Rector\Autodiscovery\Rector\Class_\MoveEntitiesToEntityDirectoryRector;
use Rector\Core\Configuration\Option;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Nette\Set\NetteSetList;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\Rector\Class_\DoctrineAnnotationClassToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Restoration\Rector\Namespace_\CompleteImportForPartialAnnotationRector;
use Rector\Restoration\ValueObject\CompleteImportForPartialAnnotation;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
//    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);

    // Define what rule sets will be applied
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SymfonySetList::SYMFONY_52);
    $containerConfigurator->import(DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(NetteSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(DoctrineSetList::DOCTRINE_BEHAVIORS_20);

    // get services (needed for register a single rule)
    $services = $containerConfigurator->services();

    // register a single rule
    $services->set(TypedPropertyRector::class);
    $services->set(MoveEntitiesToEntityDirectoryRector::class);
    $services->set(Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class);
    $services->set(Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class);
    $services->set(Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector::class);
    $services->set(Rector\PostRector\Rector\UseAddingPostRector::class);
    $services->set(Rector\PostRector\Rector\NameImportingPostRector::class);
    $services->set(DoctrineAnnotationClassToAttributeRector::class)
        ->call('configure', [[
            DoctrineAnnotationClassToAttributeRector::REMOVE_ANNOTATIONS => true,
        ]]);

    $services->set(AnnotationToAttributeRector::class)
        ->call('configure', [[
            AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
                new AnnotationToAttribute('Symfony\Component\Routing\Annotation\Route'),
            ]),
        ]]);

    $services->set(TypedPropertyRector::class)
        ->call('configure', [[
            TypedPropertyRector::CLASS_LIKE_TYPE_ONLY => false,
            TypedPropertyRector::PRIVATE_PROPERTY_ONLY => false,
        ]]);


    $parameters->set(Option::APPLY_AUTO_IMPORT_NAMES_ON_CHANGED_FILES_ONLY, true);
//    $services->set(AnnotationToAttributeRector::class)
//        ->call('configure', [[
//            AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
//                new AnnotationToAttribute('ApiPlatform\Core\Annotation\ApiResource'),
//                new AnnotationToAttribute('Gedmo\Tree', 'Gedmo\Mapping\Annotation\Tree'),
//                new AnnotationToAttribute('Gedmo\Slug', 'Gedmo\Mapping\Annotation\Slug'),
//                new AnnotationToAttribute('Gedmo\TreeLeft', 'Gedmo\Mapping\Annotation\TreeLeft'),
//                new AnnotationToAttribute('Gedmo\TreeRight', 'Gedmo\Mapping\Annotation\TreeRight'),
//                new AnnotationToAttribute('Gedmo\TreeParent', 'Gedmo\Mapping\Annotation\TreeParent'),
//                new AnnotationToAttribute('Gedmo\TreeRoot', 'Gedmo\Mapping\Annotation\TreeRoot'),
//                new AnnotationToAttribute('Gedmo\TreeLevel', 'Gedmo\Mapping\Annotation\TreeLevel'),
//            ]),
//        ]]);
};
