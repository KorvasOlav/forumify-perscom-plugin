<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Component;

use Forumify\Core\Component\Table\AbstractTable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Twig\Environment;

#[AsLiveComponent('PerscomSubmissionTable', '@Forumify/components/table/table.html.twig')]
#[IsGranted('perscom-io.admin.submissions.view')]
class PerscomSubmissionTable extends AbstractPerscomTable
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
    ) {
        $this->sort = ['created_at' => AbstractTable::SORT_DESC];

        $formSearch = $this->requestStack->getCurrentRequest()?->get('form');
        if ($formSearch !== null) {
            $this->search = ['form__name' => $formSearch];
        }
    }

    protected function buildTable(): void
    {
        $this
            ->addColumn('user__name', [
                'field' => '[user?][name]',
                'label' => 'Name',
            ])
            ->addColumn('form__name', [
                'field' => '[form?][name]',
                'label' => 'Form',
            ])
            ->addColumn('status', [
                'field' => '[statuses][0?]',
                'searchable' => false,
                'sortable' => false,
                'renderer' => fn ($status) => $status !== null
                    ? $this->twig->render('@ForumifyPerscomPlugin/frontend/roster/components/status.html.twig', [
                        'class' => 'text-small',
                        'status' => $status
                    ])
                    : '',
            ])
            ->addColumn('created_at', [
                'field' => '[created_at]',
                'label' => 'Created At',
                'searchable' => false,
                'renderer' => fn (string $date) => $this->translator->trans('date_time_short', ['date' => new \DateTime($date)]),
            ])
            ->addColumn('actions', [
                'label' => '',
                'renderer' => fn ($_, $row) => $this->twig->render('@ForumifyPerscomPlugin/admin/submissions/list/actions.html.twig', [
                    'submission' => $row,
                ]),
                'searchable' => false,
                'sortable' => false,
            ]);
    }

    protected function getData(int $limit, int $offset, array $search, array $sort): array
    {
        $data = parent::getData($limit, $offset, $search, $sort);
        foreach ($data as &$row) {
            usort($row['statuses'], static fn ($a, $b) => $b['record']['updated_at'] <=> $a['record']['updated_at']);
        }

        return $data;
    }

    protected function getResource(): string
    {
        return 'submissions';
    }

    protected function getInclude(): array
    {
        return ['user', 'form', 'statuses', 'statuses.record'];
    }
}
