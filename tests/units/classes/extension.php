<?php

namespace mageekguy\atoum\blackfire\tests\units;

use
    mageekguy\atoum,
    mageekguy\atoum\blackfire\extension as testedClass
    ;

class extension extends atoum\test
{

    public function testSetTest()
    {
        $this
            ->if($extension = new testedClass())
                ->and($test = new \mock\mageekguy\atoum\test())
                ->and($manager = new \mock\mageekguy\atoum\test\assertion\manager())
                ->and($test->setAssertionManager($manager))
            ->then
                ->object($extension->setTest($test))->isIdenticalTo($extension)
                ->mock($manager)
                    ->call('setHandler')->withArguments('blackfireProfile')->once()
        ;
    }
}
