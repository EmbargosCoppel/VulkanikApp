<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StripLeadingGarbage
{
    /**
     * Handle an incoming request and strip any accidental text before the HTML document.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $content = $response->getContent();

            if (is_string($content)) {
                // Find first occurrence of DOCTYPE or <html and strip anything before it
                $posDoctype = stripos($content, '<!doctype');
                $posHtml = stripos($content, '<html');

                $pos = false;
                if ($posDoctype !== false) $pos = $posDoctype;
                if ($pos === false && $posHtml !== false) $pos = $posHtml;

                if ($pos !== false && $pos > 0) {
                    $new = substr($content, $pos);
                    $response->setContent($new);
                }
            }
        } catch (\Exception $e) {
            // If anything fails, do nothing to avoid breaking responses
        }

        return $response;
    }
}
