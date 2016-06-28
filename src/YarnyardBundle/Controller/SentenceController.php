<?php

namespace YarnyardBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Exception\YarnyardException;

class SentenceController extends AbstractRestController
{
    /**
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"sentence"})
     * @Rest\Route("sentences")
     *
     * @param Request $request
     *
     * @return Story
     */
    public function postSentenceAction(Request $request)
    {
        $text = $request->request->get('text');
        $storyId = $request->request->get('storyId');
        $story = $this->get('story.repository')->find($storyId);

        if (!$story) {
            throw new YarnyardException('that story does not exist');
        }

        return $this->get('sentence.service')->create($story, $text);
    }
}
