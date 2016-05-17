<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YarnyardBundle\Util\Pagination\PaginationHelper;

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

        $maxPerPage = $request->query->get(PaginationHelper::KEY_MAX_PER_PAGE, PaginationHelper::DEFAULT_MAX);
        $pagerfanta->setMaxPerPage($maxPerPage);

        $currentPage = $request->query->get(PaginationHelper::KEY_PAGE, 1);
        $pagerfanta->setCurrentPage($currentPage);

        header('link: ' . PaginationHelper::getPaginationHeaders($request, $pagerfanta)->get('Link'));

        return $pagerfanta->getCurrentPageResults();
    }
}
