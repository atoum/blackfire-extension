<?php
namespace mageekguy\atoum\blackfire;

use Blackfire\Client;
use mageekguy\atoum;
use mageekguy\atoum\observable;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

class extension implements atoum\extension
{

    /*protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
*/
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
                function($client, $callback, $configuration) use ($test, & $asserter) {
                    if ($asserter === null)
                    {
                        $asserter = new atoum\blackfire\asserters\blackfire($test->getAsserterGenerator());
                    }
                    return $asserter->setWith($client, $callback, $configuration);
                }
            )
        ;
        return $this;
    }

    public function handleEvent($event, observable $observable) {}
}
