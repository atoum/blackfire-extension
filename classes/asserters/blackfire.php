<?php

namespace mageekguy\atoum\blackfire\asserters;

use Blackfire\Client;
use
    mageekguy\atoum\asserters,
    mageekguy\atoum\exceptions,
    Blackfire\Exception\ExceptionInterface
;

class blackfire extends asserters\object
{
    /**
     * @var \Blackfire\Profile
     */
    protected $lastProfile;

    public function setWith($client, $callback = null, $config = null)
    {
        if (!($client instanceof \Blackfire\Client))
        {
            $this->fail($this->_('%s is not a blackfire client', $this));
        }

        if (!($callback instanceof \closure))
        {
            $this->fail($this->_('%s is not a closure', $this));
        }

        if (!($config instanceof \Blackfire\Profile\Configuration))
        {
            $this->fail($this->_('%s is not a profile configuration', $this));
        }

        try {
            $probe = $client->createProbe($config);

            $callback();

            $this->lastProfile = $client->endProbe($probe);

        } catch (ExceptionInterface $e) {
            $this->fail($e->getMessage());
        }

        parent::setWith(new \ArrayObject(), false);

        return $this;
    }

    public function matchesConfigAssertion()
    {
        if (null === $this->lastProfile) {
            $this->fail($this->_('No profile found'));
        }

        if ($this->lastProfile->isSuccessful()) {
            echo 'pass';
            //$this->pass();
            return $this;
        }

        $failureDescription = sprintf('An error occurred when profiling the test. More information at %s', $this->lastProfile->getUrl());

        $tests = $this->lastProfile->getTests();

        $failures = 0;
        $details = '';
        foreach ($tests as $test) {
            if ($test->isSuccessful()) {
                continue;
            }

            ++$failures;
            $details .= sprintf("    %s: %s\n", $test->getState(), $test->getName());
            foreach ($test->getFailures() as $assertion) {
                $details .= sprintf("      - %s\n", $assertion);
            }
        }
        $details .= sprintf("\nMore information at %s.", $this->lastProfile->getUrl());

        $failureDescription = "Failed asserting that Blackfire tests pass.\n";
        $failureDescription .= sprintf("%d tests failures out of %d.\n\n", $failures, count($tests));
        $failureDescription .= $details;

        $this->fail($failureDescription);

        return $this;
    }
}
