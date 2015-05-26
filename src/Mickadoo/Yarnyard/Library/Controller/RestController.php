<?php

namespace Mickadoo\Yarnyard\Library\Controller;

use FOS\OAuthServerBundle\Model\TokenInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\EntityRepository\RepositoryTrait;
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
     * @param QueryBuilder $queryBuilder
     * @return Response
     */
    protected function paginate(QueryBuilder $queryBuilder)
    {
        // todo cleanup, move to RestController

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        $body = $this->get('serializer')->serialize($pagerfanta->getCurrentPageResults()->getArrayCopy(), 'json');

        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $nextPageNumber = $pagerfanta->hasNextPage() ? $pagerfanta->getNextPage() : null;
        $maxPerPage = $pagerfanta->getMaxPerPage();
        $lastPage = $pagerfanta->getNbPages();

        $nextPageUrl = $pagerfanta->hasNextPage() ? "$currentUrl&per_page=$maxPerPage&page=$nextPageNumber" : null;
        $lastPageUrl = "$currentUrl&per_page=$maxPerPage&page=$lastPage";

        $response = new Response(
            $body,
            200,
            ["Link" => "<$nextPageUrl>; rel='next', <$lastPageUrl>; rel='last'"]
        );

        return $response;
    }

}