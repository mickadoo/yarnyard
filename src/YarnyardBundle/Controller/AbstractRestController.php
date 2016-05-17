<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YarnyardBundle\Util\Pagination\PaginationHeaderGenerator;

abstract class AbstractRestController extends FOSRestController
{
    /**
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     *
     * @return Response
     */
    protected function paginate(Request $request, QueryBuilder $queryBuilder)
    {
        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));

        $maxPerPage = $request->query->get(
            PaginationHeaderGenerator::KEY_MAX_PER_PAGE,
            PaginationHeaderGenerator::DEFAULT_MAX
        );

        $pagerfanta->setMaxPerPage($maxPerPage);

        $currentPage = $request->query->get(PaginationHeaderGenerator::KEY_PAGE, 1);
        $pagerfanta->setCurrentPage($currentPage);

        $generator = $this->get('pagination_header.generator');
        header('link: ' . $generator->getPaginationHeaders($request, $pagerfanta)->get('Link'));

        return $pagerfanta->getCurrentPageResults();
    }
}
