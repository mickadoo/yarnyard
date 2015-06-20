<?php

namespace Mickadoo\Yarnyard\Library\UrlHelper;

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
        $existingQueryString = $urlParts['query'];

        $existingQueryStringParts = [];
        if (!empty($existingQueryString)) {
            parse_str($existingQueryString, $existingQueryStringParts);
        }

        $newQueryString = http_build_query(array_merge($queryParts, $existingQueryStringParts));

        return str_replace($existingQueryString, $newQueryString, $existingUrl);
    }
}
