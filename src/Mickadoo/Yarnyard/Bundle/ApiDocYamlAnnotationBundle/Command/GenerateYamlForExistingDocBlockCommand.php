<?php

namespace Mickadoo\Yarnyard\Bundle\ApiDocYamlAnnotationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class GenerateYamlForExistingDocBlockCommand extends ContainerAwareCommand
{
    const NAME = 'yamldoc:generate';

    protected function configure()
    {
        $this->setName(self::NAME);
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $request = Request::create('/api/doc');
        $this->getContainer()->get('kernel')->handle($request);
    }
}