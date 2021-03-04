<?php

namespace Xanweb\PhpCsFixer;

use Concrete\Core\Support\CodingStyle\PhpFixer as CorePhpFixer;
use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpFixer extends CorePhpFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(InputInterface $input, OutputInterface $output, array $paths, $dryRun = false)
    {
        $pathRuleList = [];
        foreach ($paths as $path) {
            $pathRuleList = $this->mergePathAndFlags($pathRuleList, $this->splitPathToPathAndFlags($path));
        }
        $this->runner->resetSteps();
        foreach ($pathRuleList as $flags => $_paths) {
            $this->runner->addStep($this->options, $_paths, $flags);
        }

        // Add missing width parameter to fit php-cs-fixer v2.14.*
        $progressOutput = new ProcessOutput($output, $this->runner->getEventDispatcher(), 132, $this->runner->calculateNumberOfFiles());
        $counters = [];
        $counter = function (FixerFileProcessedEvent $e) use (&$counters) {
            $status = $e->getStatus();
            if (isset($counters[$status])) {
                ++$counters[$status];
            } else {
                $counters[$status] = 1;
            }
        };
        $this->runner->getEventDispatcher()->addListener(FixerFileProcessedEvent::NAME, $counter);
        try {
            $progressOutput->printLegend();
            $changes = $this->runner->apply($dryRun);
            $output->writeln('');
        } finally {
            $this->runner->getEventDispatcher()->removeListener(FixerFileProcessedEvent::NAME, $counter);
        }

        return [$counters, $changes, clone $this->runner->getErrorManager()];
    }
}
