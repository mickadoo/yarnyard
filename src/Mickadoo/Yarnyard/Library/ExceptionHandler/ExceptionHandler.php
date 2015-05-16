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
        $responseBody = $this->getResponseBody($exception);
        $code = $exception->getCode() ? (int) $exception->getCode() : 400;

        $response = new Response($responseBody, $code);
        $event->setResponse($response);
    }

    private function getResponseBody(\Exception $exception)
    {
        $responseBody = array('error' => ['message' => $exception->getMessage()]);

        if ($exception instanceof NonCriticalException) {
            // todo translate message
            $responseBody['error']['key'] = $exception->getKey();
        }

        return json_encode($responseBody);
    }

}