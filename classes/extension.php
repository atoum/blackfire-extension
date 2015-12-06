<?php

namespace mageekguy\atoum\blackfire;

use Blackfire\Client;
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
