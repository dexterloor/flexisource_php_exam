<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

// Exceptions
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $data = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        ];

        $http_code = $exception->getCode();
        if ($exception instanceof BadRequestHttpException) {
            $http_code = 400;
        }

        $response = new JsonResponse($data, ($http_code == 0 || $http_code > 500) ? 500 : $http_code);

        $event->setResponse($response);
    }
}