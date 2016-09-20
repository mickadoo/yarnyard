<?php

namespace YarnyardBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Exception\YarnyardException;

class ParticipationGrantController extends AbstractRestController
{
    /**
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"participationGrant"})
     * @Rest\Route("participation-grants")
     *
     * @param Request $request
     *
     * @return ParticipationGrant
     *
     * @throws YarnyardException
     */
    public function postGrantAction(Request $request)
    {
        $userId = (int) $request->request->get('userId');
        $storyId = (int) $request->request->get('storyId');

        $user = $this->get('user.repository')->find($userId);
        $story = $this->get('story.repository')->find($storyId);

        if (!$user) {
            throw new YarnyardException('user not found');
        }

        if (!$story) {
            throw new YarnyardException('story not found');
        }

        return $this->get('participation_grant.creator')->create($story, $user);
    }
}
