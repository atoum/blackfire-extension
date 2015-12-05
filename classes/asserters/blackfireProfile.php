<?php

namespace mageekguy\atoum\blackfire\asserters;

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use Blackfire\Exception\ExceptionInterface;

use
    mageekguy\atoum\asserters,
    mageekguy\atoum\exceptions
;

class blackfireProfile extends asserters\object
{
    /**
     * @param Client $client
     * @param \closure $callback
     * @param Configuration $config
     *
     * @return $this
     */
    public function setWith($client, $callback = null, $config = null)
    {
        if (!($client instanceof Client))
        {
            $this->fail($this->_('%s is not a blackfire client', $this));
        }

        if (!($callback instanceof \closure))
        {
            $this->fail($this->_('%s is not a closure', $this));
        }

        if (!($config instanceof Configuration))
        {
            $this->fail($this->_('%s is not a profile configuration', $this));
        }

        try {
            $probe = $client->createProbe($config);

            $callback();

            $profile = $client->endProbe($probe);

        } catch (ExceptionInterface $e) {
            $this->fail($e->getMessage());
        }

        parent::setWith($profile);

        return $this;
    }

    /**
     * @return $this
     */
    public function matchesAssertions()
    {
        $profile = $this->valueIsSet()->value;

        if ($isSuccessFul = $profile->isSuccessful())
        {
            return $this->pass();
        }

        $failureDescription = sprintf('An error occurred when profiling the test. More information at %s', $profile->getUrl());

        $tests = $profile->getTests();

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
        $details .= sprintf("\nMore information at %s.", $profile->getUrl());

        $failureDescription = "Failed asserting that Blackfire tests pass.\n";
        $failureDescription .= sprintf("%d tests failures out of %d.\n\n", $failures, count($tests));
        $failureDescription .= $details;

        $this->fail($failureDescription);
    }
}
