<?php

namespace App\Command;

use App\Domain\Entity\Company\ResourceCategory;
use App\Domain\Entity\Showcase\ShowcaseCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'workface:category:tree:checker',
    description: 'Check category tree state on root category.',
)]
class CheckAndUpdateCategoryTreesCommand extends Command
{
    const OPTION_DRY_RUN = 'dry-run';

    const TREE_ENTITIES_WITH_DEFAULT_ROOT = [
        ShowcaseCategory::class => 'showcase',
        ResourceCategory::class => 'company'
    ];

    public function __construct(private EntityManagerInterface $entityManager, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('workface:category:tree:checker')
            ->addOption(self::OPTION_DRY_RUN, null, InputOption::VALUE_NONE, 'Option emulated working.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /**
         * @var ShowcaseCategory|ResourceCategory $treeEntity
         */
        foreach (self::TREE_ENTITIES_WITH_DEFAULT_ROOT as $treeEntity => $relationField) {
            $relationsIds = $this->getRelationsIds($treeEntity, $relationField);
            $moved[$treeEntity] = 0;

            if (!count($relationsIds)) {
                continue;
            }
            foreach ($relationsIds as $relationId) {
                $roots = $this->getRootCategoriesOwner($treeEntity, $relationField, $relationId);
                $existDefaultRoot = $this->existDefaultRoot($roots);

                if (count($roots) === 1 && $existDefaultRoot !== null) {
                    continue;
                }
                if ($existDefaultRoot === null) {
                    $method = 'get' . \ucfirst($relationField);                    
                    $existDefaultRoot = $treeEntity::create(
                        $treeEntity::ROOT_CATEGORY_NAME,
                        $treeEntity::ROOT_CATEGORY_DESCRIPTION,
                        $roots[0]->$method()
                    );
                    $this->entityManager->persist($existDefaultRoot);
                }
                foreach ($roots as $root) {
                    if (
                        $root->getTitle() == $root::ROOT_CATEGORY_NAME
                        && $root->getDescription() == $root::ROOT_CATEGORY_DESCRIPTION
                    ) {
                        continue;
                    }
                    $root->setParent($existDefaultRoot);
                    $this->entityManager->persist($root);
                    ++$moved[$treeEntity];
                }
            }
        }
        if (!$input->getOption(self::OPTION_DRY_RUN)) {
            $this->entityManager->flush();
        }
        $io->success('Moved children - ' . print_r($moved, true));

        return Command::SUCCESS;
    }

    private function getRelationsIds(string $treeEntity, string $relationField): array
    {
        $relations = $this->entityManager->createQueryBuilder()
            ->select('r, rr.id')
            ->distinct()
            ->from($treeEntity, 'r')
            ->innerJoin('r.' . $relationField, 'rr')
            ->where('r.parent IS NULL')
            ->getQuery()
            ->getArrayResult();

        return array_unique(array_column($relations, 'id'));
    }

    private function getRootCategoriesOwner(string $treeEntity, string $relationField, int $relationId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r')
            ->distinct()
            ->from($treeEntity, 'r')
            ->innerJoin('r.' . $relationField, 'rr')
            ->where('r.parent IS NULL')
            ->andWhere('r.' . $relationField . ' = :id')
            ->setParameter('id', $relationId)
            ->getQuery()
            ->getResult();
    }

    private function existDefaultRoot(array $rootCategories): ?object
    {
        foreach ($rootCategories as $root) {
            if (
                $root->getTitle() == $root::ROOT_CATEGORY_NAME
                && $root->getDescription() == $root::ROOT_CATEGORY_DESCRIPTION
            ) {
                return $root;
            }
        }
        return null;
    }
}
