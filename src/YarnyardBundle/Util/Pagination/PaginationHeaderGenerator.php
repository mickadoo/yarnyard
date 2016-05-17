<?php

namespace YarnyardBundle\Util\Pagination;

use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use YarnyardBundle\Util\Request\QueryStringRebuilder;

class PaginationHeaderGenerator
{
    const KEY_MAX_PER_PAGE = 'per_page';
    const KEY_PAGE = 'page';
    const DEFAULT_MAX = 10;

    /**
     * @var QueryStringRebuilder
     */
    protected $queryRebuilder;

    /**
     * @param QueryStringRebuilder $queryRebuilder
     */
    public function __construct(QueryStringRebuilder $queryRebuilder)
    {
        $this->queryRebuilder = $queryRebuilder;
    }

    /**
     * @param Request $request
     * @param Pagerfanta $pagerfanta
     *
     * @return ResponseHeaderBag
     */
    public function getPaginationHeaders(Request $request, Pagerfanta $pagerfanta)
    {
        // todo doesn't work in test
        $currentUrl = $request->getUri();
        $maxPerPage = $pagerfanta->getMaxPerPage();

        $currentPageNumber = $pagerfanta->getCurrentPage();
        $lastPageNumber = $pagerfanta->getNbPages();

        $currentPageParts = [
            self::KEY_MAX_PER_PAGE => $maxPerPage,
            self::KEY_PAGE => $currentPageNumber,
        ];

        $lastPageParts = [
            self::KEY_MAX_PER_PAGE => $maxPerPage,
            self::KEY_PAGE => $lastPageNumber,
        ];

        $linkHeaderString = sprintf(
            '<%s>;rel="%s",<%s>;rel="%s"',
            $this->queryRebuilder->addQueryToUrl($currentUrl, $currentPageParts),
            'self',
            $this->queryRebuilder->addQueryToUrl($currentUrl, $lastPageParts),
            'last'
        );

        $headers = new ResponseHeaderBag(['Link' => $linkHeaderString]);

        return $headers;
    }
}
