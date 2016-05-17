<?php

namespace YarnyardBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @return Story
     */
    public function postStoryAction(Request $request)
    {
        $title = $request->request->get('title');

        $story = new Story();
        $story->setTitle($title);

        $this->get('doctrine.orm.default_entity_manager')->persist($story);
        $this->get('doctrine.orm.default_entity_manager')->flush($story);

        return $story;
    }
}