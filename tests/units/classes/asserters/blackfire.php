<?php

namespace mageekguy\atoum\blackfire\asserters\tests\units;

use
    mageekguy\atoum,
    mageekguy\atoum\blackfire\asserters\blackfire as testedClass
;

class blackfire extends atoum\test
{

    public function testSetWithWithoutClient()
    {
        $this
            ->exception(function() {
                $testedClass = new testedClass();
                $testedClass->profile(function () {});
            })
            ->hasMessage('Client has not been set')
            ->isInstanceOf('\LogicException')
        ;
    }

    public function testSetWithWithoutClosure()
    {
        $this
            ->exception(function() {
                $client = new \mock\Blackfire\Client();
                $testedClass = new testedClass();
                $testedClass->setClient($client);
                $testedClass->profile(new \ArrayObject());
            })
            ->hasMessage('profile parameter is not a closure')
            ->isInstanceOf('\mageekguy\atoum\asserter\exception')
        ;
    }

    public function testAll()
    {
        $this
            ->mockGenerator->shuntParentClassCalls()
            ->mockGenerator->orphanize('__construct')
            ->given(
                $probeMock = new \mock\Blackfire\Probe(),
                $clientMock = new \mock\Blackfire\Client()
            )
            ->mockGenerator->unshuntParentClassCalls()
            ->given(
                ($this->calling($clientMock)->createProbe = $probeMock),
                ($this->calling($clientMock)->endProbe = $this->getErrorProfile()),
                $config = new \Blackfire\Profile\Configuration()
            )
        ;

        $this
            ->if(
                $testedClass = new testedClass(),
                $testedClass->setConfiguration($config),
                $testedClass->setClient($clientMock)
            )
            ->then
                ->exception(function() use ($testedClass) {
                    $testedClass->profile(function () {});
                })
                    ->hasMessage($this->getExpectedErrorMessage())
                    ->isInstanceOf('\mageekguy\atoum\asserter\exception')
                ->mock($clientMock)->call('createProbe')->once()
                ->mock($clientMock)->call('endProbe')->once()
        ;


        $this
            ->given(
                $test = new \mock\mageekguy\atoum\test(),
                //$testedClassMock = new \mock\mageekguy\atoum\blackfire\asserters\blackfireProfile(),
                ($this->calling($clientMock)->endProbe = $this->getOkProfile())
            )
            ->if(
                $testedClass = new testedClass(),
                $testedClass->setWithTest($test),
                $testedClass->setClient($clientMock),
                $testedClass->profile(function() {})
            )
        ;
    }

    protected function getExpectedErrorMessage()
    {
        return <<<EOF
Failed asserting that Blackfire tests pass.
1 tests failures out of 1.

    failed: Temps d'execution
      - main.wall_time 4s < 1s

More information at https://blackfire.io/profiles/a6337421-337a-47c3-a1ef-35f606883edd/graph.
EOF;
    }

    protected function getErrorProfile()
    {
        return new \Blackfire\Profile(include __DIR__ . '/_data/error_profile.php');
    }

    protected function getOkProfile()
    {
        return new \Blackfire\Profile(include __DIR__ . '/_data/ok_profile.php');
    }
}