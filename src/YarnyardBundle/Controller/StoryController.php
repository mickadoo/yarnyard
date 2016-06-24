<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\Review;
use YarnyardBundle\Entity\Sentence;
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
        $random = $request->request->get('random');
        $random = filter_var($random, FILTER_VALIDATE_BOOLEAN);
        $rounds = (int) $request->request->get('rounds');

        return $this->get('story.service')->create($title, $random, $rounds);
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
        $contributorId = $request->query->get('contributorId');
        $granteeId = $request->query->get('granteeId');
        $sortBy = $request->query->get('sortBy');

        if ($granteeId) {
            $query
                ->leftJoin(
                    ParticipationGrant::class,
                    'grant',
                    'WITH',
                    'grant.story = story.id'
                )
                ->andWhere('grant.user = :grantee')
                ->setParameter('grantee', $granteeId);
        }

        if ($contributorId) {
            $query
                ->leftJoin(
                    Sentence::class,
                    'sentence',
                    'WITH',
                    'sentence.story = story.id'
                )
                ->andWhere('sentence.createdBy = :contributor')
                ->setParameter('contributor', $contributorId);
        }

        if ($sortBy === 'rating') {
            $query
                ->leftJoin(
                    Review::class,
                    'review',
                    'WITH',
                    'review.story = story.id'
                )
                ->addSelect('AVG(review.rating) AS HIDDEN rating');
        }

        return $this->paginate($request, $query);
    }
}
