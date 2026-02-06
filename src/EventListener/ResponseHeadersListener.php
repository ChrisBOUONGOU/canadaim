<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseHeadersListener
{
    /**
     * Add security and SEO headers to all responses
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // SEO headers
        $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');

        // HSTS for HTTPS (if using SSL)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remove sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('X-AspNet-Version');
        $response->headers->remove('Server');
    }
}
