<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use YarnyardBundle\Util\Pagination\PaginationHelper;

abstract class AbstractRestController extends FOSRestController
{
    /**
     * @param $route
     * @return Response
     */
    public function handleSubRequest($route)
    {
        $_SERVER['REQUEST_URI'] = $route;
        $request = Request::createFromGlobals();
        $response = $this->get('kernel')->handle($request, HttpKernelInterface::SUB_REQUEST);

        return $response;
    }

    /**
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     * @return Response
     */
    protected function paginate(Request $request, QueryBuilder $queryBuilder)
    {
        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));

        $maxPerPage = $request->query->get(PaginationHelper::KEY_MAX_PER_PAGE, PaginationHelper::DEFAULT_MAX);
        $pagerfanta->setMaxPerPage($maxPerPage);

        $currentPage = $request->query->get(PaginationHelper::KEY_PAGE, 1);
        $pagerfanta->setCurrentPage($currentPage);

        /** @var \ArrayIterator $results */
        $results = $pagerfanta->getCurrentPageResults();
        $body = $this->get('serializer')->serialize($results->getArrayCopy(), 'json');

        $response = new Response($body);
        $response->headers = PaginationHelper::getPaginationHeaders($request, $pagerfanta);

        return $response;
    }
}