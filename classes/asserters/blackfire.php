<?php

namespace mageekguy\atoum\blackfire\asserters;

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use Blackfire\Exception\ExceptionInterface;
use Blackfire\Profile;
use mageekguy\atoum\asserter;
use mageekguy\atoum\locale;
use mageekguy\atoum\tools\variable;

class blackfire extends asserter
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Client|null
     */
    protected $client;

    /**
     * @param asserter\generator $generator
     * @param variable\analyzer $analyzer
     * @param locale $locale
     */
    public function __construct(asserter\generator $generator = null, variable\analyzer $analyzer = null, locale $locale = null)
    {
        parent::__construct($generator, $analyzer, $locale);
        $this->configuration = new Configuration();
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return $this|mixed
     */
    public function __call($method, $arguments)
    {
        switch ($method)
        {
            case 'defineMetric':
            case 'defineLayer':
                call_user_func_array(array($this->configuration, $method), $arguments);

                return $this;
            default:
                return parent::__call($method, $arguments);
        }
    }

    /**
     * @param string $assertion
     * @param string $name
     *
     * @return $this
     */
    public function assert($assertion, $name = '')
    {
        $this->configuration->assert($assertion, $name);

        return $this;
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @throws \LogicException
     *
     * @return Client|null
     */
    public function getClient()
    {
        if (null === $this->client) {
            throw new \LogicException('Client has not been set');
        }
        return $this->client;
    }

    /**
     * @param \closure $callback
     *
     * @return \mageekguy\atoum\asserters\object
     */
    public function profile($callback)
    {
        $client = $this->getClient();

        if (!($callback instanceof \closure))
        {
            $this->fail($this->_('profile parameter is not a closure'));
        }

        try {
            $probe = $client->createProbe($this->configuration);

            $callback();

            $profile = $client->endProbe($probe);

        } catch (ExceptionInterface $e) {
            $this->fail($e->getMessage());
        }

        $this->matchesAssertions($profile);

        return $this->object($profile);
    }

    /**
     * @param Profile $profile
     */
    protected function matchesAssertions(Profile $profile)
    {
        if ($isSuccessFul = $profile->isSuccessful())
        {
            $this->pass();
            return;
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
