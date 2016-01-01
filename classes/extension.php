<?php

namespace mageekguy\atoum\blackfire;

use Blackfire\Client;
use Blackfire\ClientConfiguration;
use mageekguy\atoum;
use mageekguy\atoum\observable;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

class extension implements atoum\extension
{
    /**
     * @param atoum\configurator $configurator
     */
    public function __construct(atoum\configurator $configurator = null)
    {
        if ($configurator)
        {
            $script = $configurator->getScript();
            $testHandler = function($script, $argument, $values) {
                $script->getRunner()->addTestsFromDirectory(dirname(__DIR__) . '/tests/units/classes');
            };

            $script
                ->addArgumentHandler($testHandler, array('--test-ext'))
                ->addArgumentHandler($testHandler, array('--test-it'))
            ;
        }
    }

    public function setRunner(runner $runner)
    {
        return $this;
    }

    public function setTest(test $test)
    {
        $asserter = null;

        $test->getAssertionManager()
            ->setHandler(
                'blackfire',
                function(Client $client) use ($test, & $asserter) {
                    if ($asserter === null)
                    {
                        $asserter = new atoum\blackfire\asserters\blackfire($test->getAsserterGenerator());
                    }

                    $asserter->setClient($client);
                    $asserter->setWithTest($test);

                    return $asserter;
                }
            )
        ;
        return $this;
    }

    public function handleEvent($event, observable $observable) {}
}
