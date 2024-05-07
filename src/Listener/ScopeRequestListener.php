<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle\Listener;

use PBergman\Bundle\SentryBundle\Events\ScopeEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class ScopeRequestListener
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(ScopeEvent $event)
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {

            $payload = [
                'scheme'   => $request->getScheme(),
                'hostname' => $request->getHost(),
                'uri'      => $request->getRequestUri(),
                'method'   => $request->getMethod(),
                'protocol' => $request->server->get('SERVER_PROTOCOL'),
                'headers'  => $request->headers->all(),
            ];

            if (($payload['method'] === 'POST' || $payload['method'] === 'PATCH') && !empty($request->getContent())) {
                $payload['content'] = $request->getContent();
            }

            if ($request->cookies->count() > 0) {
                $payload['cookies'] = $request->cookies->all();
            }


            $event->getScope()->setContext('request', $payload);
        }
    }

}