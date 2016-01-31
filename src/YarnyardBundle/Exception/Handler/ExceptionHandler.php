<?php

namespace YarnyardBundle\Exception\Handler;

use YarnyardBundle\Exception\Mapping\ExceptionCodeMapper;
use YarnyardBundle\Util\ArrayHelper\ArrayDecorator;
use YarnyardBundle\Exception\YarnyardException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Translation\TranslatorInterface;

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
    public function __construct(TranslatorInterface $translator, ExceptionCodeMapper $exceptionCodeMapper, ArrayDecorator $arrayHelper)
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

        $responseBody = array('error' => [
            'message' => $message
        ]);

        $event->setResponse(new Response(json_encode($responseBody), $code));
    }
}