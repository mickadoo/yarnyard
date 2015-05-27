<?php

namespace Mickadoo\Yarnyard\Library\Controller;

use FOS\OAuthServerBundle\Model\TokenInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\EntityRepository\RepositoryTrait;
use Mickadoo\Yarnyard\Library\Pagination\PaginationHelper;
use Mickadoo\Yarnyard\Library\Validator\ValidatorInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\ORM\QueryBuilder;

class RestController extends FOSRestController
{

    use RepositoryTrait;

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->getToken()->getUser();
    }

    /**
     * @return null|TokenInterface
     */
    public function getToken()
    {
        return $this->container->get('security.token_storage')->getToken();
    }

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
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function createResponseFromValidator(ValidatorInterface $validator)
    {
        $response = new Response();
        $response->setStatusCode($validator->getErrorCode());
        $responseBody = [
            'error' =>
                [
                    'key' => $validator->getErrorKey()
                ]
        ];
        $response->setContent(json_encode($responseBody));

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

        // todo replace with parameters.yml default max
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