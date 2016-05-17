<?php

namespace YarnyardBundle\Util\Request;

class QueryStringRebuilder
{
    /**
     * @param string $existingUrl
     * @param array $newQueryParts
     *
     * @return string
     */
    public function addQueryToUrl($existingUrl, array $newQueryParts)
    {
        $urlParts = parse_url($existingUrl);

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $existingQueryParts);
            $newQueryParts = array_merge($existingQueryParts, $newQueryParts);
        }

        $queryString = http_build_query($newQueryParts);

        $anchor = isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : null;
        $path = $urlParts['path'] ?? null;
        $queryString = $queryString ? '?' . $queryString : null;

        return $urlParts['scheme'] . '://' . $urlParts['host'] . $path . $queryString . $anchor;
    }
}
