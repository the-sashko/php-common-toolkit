<?php
/**
 * Class For Routing HTTP Requests
 */
class Router
{
    /**
     * Perform Redirect Rules
     *
     * @param string|null $url HTTP Request URL
     */
    public function routeRedirect(?string $url = null): void
    {
        if (empty($url)) {
            $url = '/';
        }

        if (!preg_match('/^\/$/su', $url)) {
            header('Location: /');
            exit(0);
        }
    }

    /**
     * Perform Rewrite Rules
     *
     * @param string|null $url HTTP Request URL
     *
     * @return string Rewrited HTTP Request URL
     */
    public function routeRewrite(?string $url = null): string
    {
        if (empty($url)) {
            $url = '/';
        }

        if (preg_match('/^\/$/su', $url)) {
            return '/en/main/index/';
        }

        return $url;
    }
}
