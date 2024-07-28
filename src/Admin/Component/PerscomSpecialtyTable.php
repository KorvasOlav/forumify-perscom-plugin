<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('PerscomSpecialtyTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.organization.view')]
class PerscomSpecialtyTable extends AbstractPerscomTable
{
    protected function buildTable(): void
    {
        $this
            ->addColumn('abbreviation', [
                'field' => '[abbreviation]'
            ])
            ->addColumn('name', [
                'field' => '[name]'
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn () => '',
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    protected function getResource(): string
    {
        return 'specialties';
    }
}
