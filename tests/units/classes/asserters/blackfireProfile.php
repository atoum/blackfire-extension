<?php

namespace mageekguy\atoum\blackfire\asserters\tests\units;

use
    mageekguy\atoum,
    mageekguy\atoum\blackfire\asserters\blackfireProfile as testedClass
;

class blackfireProfile extends atoum\test
{

    public function testSetWithWithoutCallback()
    {
        $this
            ->exception(function() {
                $client = new \mock\Blackfire\Client();
                $testedClass = new testedClass();
                $testedClass->setWith($client);
            })
            ->hasMessage('null is not a closure')
            ->isInstanceOf('\mageekguy\atoum\asserter\exception')
        ;
    }

    public function testSetWithWithoutConfig()
    {
        $this
            ->exception(function() {
                $client = new \mock\Blackfire\Client();
                $testedClass = new testedClass();
                $testedClass->setWith($client, function() {});
            })
            ->hasMessage('null is not a profile configuration')
            ->isInstanceOf('\mageekguy\atoum\asserter\exception')
        ;
    }

    public function testAll()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $this->mockGenerator->orphanize('__construct');

        $probeMock = new \mock\Blackfire\Probe();
        $clientMock = new \mock\Blackfire\Client();

        $this->mockGenerator->unshuntParentClassCalls();

        $this->calling($clientMock)->createProbe = $probeMock;

        $config = new \Blackfire\Profile\Configuration();
        $testedClass = new testedClass();


        $this->calling($clientMock)->endProbe = $this->getErrorProfile();

        $testedClass->setWith($clientMock, function() {}, $config);

        $this
            ->mock($clientMock)
                ->call('createProbe')
                ->once()
            ->mock($clientMock)
                ->call('endProbe')
                ->once()
        ;

        $expectedMessage = <<<EOF
Failed asserting that Blackfire tests pass.
1 tests failures out of 1.

    failed: Temps d'execution
      - main.wall_time 4s < 1s

More information at https://blackfire.io/profiles/a6337421-337a-47c3-a1ef-35f606883edd/graph.
EOF;


        $this
            ->exception(function() use ($testedClass) {
                $testedClass->matchesAssertions();
            })
            ->hasMessage($expectedMessage)
            ->isInstanceOf('\mageekguy\atoum\asserter\exception')
        ;


        $this
            ->if($test = new \mock\mageekguy\atoum\test())
            ->if($testedClassMock = new \mock\mageekguy\atoum\blackfire\asserters\blackfireProfile())
                ->and($this->calling($clientMock)->endProbe = $this->getOkProfile())
                ->and($testedClass->setWithTest($test))
                ->and($testedClass->setWith($clientMock, function() {}, $config))
                ->and($testedClass->matchesAssertions())
            ->then()
                ->integer($test->getScore()->getPassNumber())
                    ->isEqualTo(2)
        ;
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
