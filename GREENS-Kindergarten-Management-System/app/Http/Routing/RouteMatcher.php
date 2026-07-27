<?php

declare(strict_types=1);

namespace App\Http\Routing;

class RouteMatcher
{
    public function match(Route $route, string $uri): ?RouteParameters
    {
        $pattern = $this->compile($route->getUri());
        if (preg_match($pattern, $uri, $matches) !== 1) {
            return null;
        }

        $parameters = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key) && $value !== '') {
                $parameters[$key] = $value;
            }
        }

        return new RouteParameters($parameters);
    }

    private function compile(string $uri): string
    {
        $escaped = preg_quote($uri, '#');
        $escaped = str_replace('\{', '{', $escaped);
        $escaped = str_replace('\}', '}', $escaped);
        $escaped = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', '(?P<\1>[^/]+)', $escaped) ?? $escaped;

        return '#^' . $escaped . '$#';
    }
}
