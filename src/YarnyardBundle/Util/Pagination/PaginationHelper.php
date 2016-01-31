<?php

namespace YarnyardBundle\Util\Pagination;

use YarnyardBundle\Util\Request\UrlHelper;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class PaginationHelper
{

    const KEY_MAX_PER_PAGE = 'per_page';
    const KEY_PAGE = 'page';
    const DEFAULT_MAX = 10;

    /**
     * @param Request $request
     * @param Pagerfanta $pagerfanta
     * @return ResponseHeaderBag
     */
    public static function getPaginationHeaders(Request $request, Pagerfanta $pagerfanta)
    {
        // todo doesn't work in test
        $currentUrl = $request->getUri();
        $maxPerPage = $pagerfanta->getMaxPerPage();

        $currentPageNumber = $pagerfanta->getCurrentPage();
        $lastPageNumber = $pagerfanta->getNbPages();

        $currentPageQueryParts = [
            self::KEY_MAX_PER_PAGE => $maxPerPage,
            self::KEY_PAGE => $currentPageNumber
        ];

        $lastPageQueryParts = [
            self::KEY_MAX_PER_PAGE => $maxPerPage,
            self::KEY_PAGE => $lastPageNumber
        ];

        $linkHeaderString = sprintf(
            '<%s>;rel="%s",<%s>;rel="%s"',
            UrlHelper::rebuildUrlQuery($currentUrl, $currentPageQueryParts),
            'self',
            UrlHelper::rebuildUrlQuery($currentUrl, $lastPageQueryParts),
            'last'
        );

        $headers = new ResponseHeaderBag(["Link" => $linkHeaderString]);

        return $headers;
    }

}
