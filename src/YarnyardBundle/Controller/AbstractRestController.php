<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var View $annotation */
        $annotation = $request->attributes->get('_template');
        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
        $generator = $this->get('pagination_header.generator');
        $format = $request->get('_format');

        $currentPage = $request->query->get($generator::KEY_PAGE, 1);
        $maxPerPage = $request->query->get($generator::KEY_MAX_PER_PAGE, $generator::DEFAULT_MAX);

        $pagerfanta->setMaxPerPage($maxPerPage);
        $pagerfanta->setCurrentPage($currentPage);
        $results = $pagerfanta->getCurrentPageResults();

        $context = ['groups' => $annotation->getSerializerGroups()];
        $responseBody = $this->get('serializer')->serialize($results, $format, $context);

        $headers = $generator->getPaginationHeaders($request, $pagerfanta);

        return new Response($responseBody, Response::HTTP_OK, $headers);
    }
}
