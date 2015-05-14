<?php

namespace Mickadoo\Yarnyard\Library\ExceptionHandler;

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
        $responseBody =
            [
                'error' =>
                    [
                        'message' => $exception->getMessage()
                    ]
            ];
        $code = $exception->getCode() ? (int) $exception->getCode() : 400;
        $response = new Response(json_encode($responseBody), $code);
        $event->setResponse($response);
    }

}