<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class AddPaginationHeadersSubscriber implements EventSubscriberInterface
{
    public function addHeaders(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (($data = $request->attributes->get('data')) && $data instanceof Paginator) {
            $response = $event->getResponse();
            $response->headers->add([
                'X-PAGINATION-TOTAL' => $data->getTotalItems(),
                'X-PAGINATION-PER_PAGE' => $data->getItemsPerPage(),
                'X-PAGINATION-TOTAL_PAGES' => ceil($data->getTotalItems() / $data->getItemsPerPage()),
                'X-PAGINATION-CURRENT_PAGE' => $data->getCurrentPage(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'addHeaders',
        ];
    }
}
