<?php

namespace YarnyardBundle\Exception\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Translation\TranslatorInterface;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Util\ArrayDecorator;

class ExceptionHandler
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ArrayDecorator
     */
    protected $arrayHelper;

    /**
     * @param TranslatorInterface $translator
     * @param ArrayDecorator      $arrayHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        ArrayDecorator $arrayHelper
    ) {
        $this->translator = $translator;
        $this->arrayHelper = $arrayHelper;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = $exception->getMessage();
        $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof YarnyardException) {
            $keys = $this->arrayHelper->decorateKeys($exception->getContext());
            $message = $this->translator->trans($message, $keys);
            $responseCode = $exception->getCode();
        }

        $responseBody = [
            'error' => [
                'message' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
        ];

        $response = new Response(json_encode($responseBody), $responseCode);

        $event->setResponse($response);
    }
}
