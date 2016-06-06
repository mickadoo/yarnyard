<?php

namespace YarnyardBundle\Exception\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Translation\TranslatorInterface;
use YarnyardBundle\Exception\Mapping\ExceptionCodeMapper;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Util\ArrayDecorator;

class ExceptionHandler
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ExceptionCodeMapper
     */
    protected $exceptionCodeMapper;

    /**
     * @var ArrayDecorator
     */
    protected $arrayHelper;

    /**
     * @param TranslatorInterface $translator
     * @param ExceptionCodeMapper $exceptionCodeMapper
     * @param ArrayDecorator $arrayHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        ExceptionCodeMapper $exceptionCodeMapper,
        ArrayDecorator $arrayHelper
    )
    {
        $this->translator = $translator;
        $this->exceptionCodeMapper = $exceptionCodeMapper;
        $this->arrayHelper = $arrayHelper;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = $exception->getMessage();
        $code = $this->exceptionCodeMapper->getCode($exception);

        if ($exception instanceof YarnyardException) {
            $message = $this->translator->trans($message, $this->arrayHelper->decorateKeys($exception->getContext()));
        }

        $responseBody = [
            'error' => [
                'message' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
        ];

        $event->setResponse(new Response(json_encode($responseBody), $code));
    }
}
