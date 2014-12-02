<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\CommandEvent;
use GuzzleHttp\Command\Event\ProcessEvent;
use GuzzleHttp\Event\SubscriberInterface;
use Otg\Ean\EanErrorException;

class EanError implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['process' => ['onProcess', 'first']];
    }

    public function onProcess(ProcessEvent $event)
    {
        $trans = $event->getTransaction();
        if (isset($trans->exception)){
            throw $trans->exception;
        }

        $response = $event->getResponse()->xml();

        $eanError = $response->EanError ?: $response->EanWsError;
        if ($eanError) {

            $e = new EanErrorException((string) $eanError->presentationMessage, $trans);

            $e->setHandling((string) $eanError->handling);
            $e->setCategory((string) $eanError->category);
            $e->setVerboseMessage((string) $eanError->verboseMessage);
            $e->setItineraryId((string) $eanError->itineraryId);

            $trans->serviceClient->getEmitter()->emit('error', $event);
            if (!$event->isPropagationStopped()) {
                throw $e;
            }
        }
    }
}
