<?php

namespace Mickadoo\YamlApiDocAnnotationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class GenerateYamlForExistingDocBlockCommand extends ContainerAwareCommand
{
    const NAME = 'yamldoc:generate';

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Create yaml annotations for existing api doc block sections in the code');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $request = Request::create('/api/doc');
        $this->getContainer()->get('kernel')->handle($request);
    }
}