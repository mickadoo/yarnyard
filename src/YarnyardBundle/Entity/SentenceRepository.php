<?php

namespace YarnyardBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SentenceRepository extends EntityRepository
{
    /**
     * @param Story $story
     *
     * @return Sentence
     */
    public function getMostRecent(Story $story) : Sentence
    {
        $query = $this
            ->createQueryBuilder('sentence')
            ->where('sentence.story = :story')
            ->setParameter('story', $story)
            ->orderBy('sentence.createdAt');

        return array_values($query->getQuery()->getResult())[0];
    }

    /**
     * @param Story $story
     *
     * @return bool
     */
    public function hasSentences(Story $story) : bool
    {
        $query = $this
            ->createQueryBuilder('sentence')
            ->select('COUNT(sentence.id)')
            ->where('sentence.story = :story')
            ->setParameter('story', $story);

        return $query->getQuery()->getSingleScalarResult() > 0;
    }
}
