<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\Story;

class StoryController extends AbstractRestController
{
    /**
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"story"})
     * @Rest\Route("stories")
     *
     * @param Request $request
     *
     * @return Story
     */
    public function postStoryAction(Request $request)
    {
        $title = $request->request->get('title');

        $story = new Story();
        // todo create story service
        $story
            ->setTitle($title)
            ->setIsCompleted(false)
            ->setContributionMode(1);

        $this->get('doctrine.orm.default_entity_manager')->persist($story);
        $this->get('doctrine.orm.default_entity_manager')->flush($story);

        return $story;
    }

    /**
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"story"})
     * @Rest\Route("stories")
     *
     * @ParamConverter("query", options={"class"="YarnyardBundle\Entity\Story"})
     *
     * @param QueryBuilder $query
     * @param Request      $request
     *
     * @return Story[]
     */
    public function getStoriesAction(QueryBuilder $query, Request $request)
    {
        return $this->paginate($request, $query);
    }
}
