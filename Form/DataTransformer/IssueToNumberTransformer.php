<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Issue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IssueToNumberTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($issue)
    {
        if (null === $issue) {
            return '';
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $issueNumber
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($issueNumber)
    {
        
        if (!$issueNumber) {
            return;
        }

        $issue = $this->em->getRepository(Issue::class)->find($issueNumber);

        if (null === $issue) {
            throw new TransformationFailedException( sprintf('An issue with number "%s" does not exist!', $issueNumber) );
        }

        return $issue;
    }
}