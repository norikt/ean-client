<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\PreparedEvent;
use GuzzleHttp\Event\SubscriberInterface;

class ContentLength  implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['prepared' => ['onPrepared', 'last']];
    }

    public function onPrepared(PreparedEvent $event)
    {
        $event->getRequest()->setHeader('Content-Length', '0');
    }
}
