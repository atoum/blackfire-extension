<?php

namespace mageekguy\atoum\blackfire;

use mageekguy\atoum;
use mageekguy\atoum\observable;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

class extension implements atoum\extension
{

    public function setRunner(runner $runner)
    {
        return $this;
    }

    public function setTest(test $test)
    {
        $asserter = null;

        $test->getAssertionManager()
            ->setHandler(
                'blackfireProfile',
                function($client, $callback, $configuration) use ($test, & $asserter) {
                    if ($asserter === null)
                    {
                        $asserter = new atoum\blackfire\asserters\blackfireProfile($test->getAsserterGenerator());
                    }
                    return $asserter->setWith($client, $callback, $configuration);
                }
            )
        ;
        return $this;
    }

    public function handleEvent($event, observable $observable) {}
}
