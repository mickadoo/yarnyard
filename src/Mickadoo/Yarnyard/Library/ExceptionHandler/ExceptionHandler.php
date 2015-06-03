<?php

namespace Mickadoo\Yarnyard\Library\ExceptionHandler;

use Mickadoo\Yarnyard\Library\Exception\NonCriticalException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionHandler
{

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (! $exception instanceof NonCriticalException) {
            // ignore and allow other listeners handle it
            return;
        }

        $responseBody = array('error' => [
            'message' => $exception->getMessage(),
            'key' => $exception->getKey()
        ]);

        $event->setResponse(new Response(json_encode($responseBody), $exception->getCode()));
    }

}