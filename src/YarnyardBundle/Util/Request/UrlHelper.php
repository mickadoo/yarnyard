<?php

namespace YarnyardBundle\Util\Request;

abstract class UrlHelper
{
    /**
     * @param string $existingUrl
     * @param array $queryParts
     * @return string
     */
    public static function rebuildUrlQuery($existingUrl, array $queryParts)
    {
        $urlParts = parse_url($existingUrl);
        $existingQueryString = isset($urlParts['query']) ? $urlParts['query'] : null;

        $existingQueryStringParts = [];
        if (!empty($existingQueryString)) {
            parse_str($existingQueryString, $existingQueryStringParts);
        }

        $newQueryString = http_build_query(array_merge($queryParts, $existingQueryStringParts));

        // todo this was broken when no query string was previously set - needs clean up
        if ($existingQueryStringParts) {
            return str_replace($existingQueryString, $newQueryString, $existingUrl);
        } else {
            return $existingUrl . '?' . $newQueryString;
        }
    }
}
