<?php

namespace mageekguy\atoum\blackfire;

use Blackfire\ClientConfiguration;
use mageekguy\atoum\observable;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

class configuration implements \mageekguy\atoum\extension\configuration
{
    /**
     * @var
     */
    private $clientConfiguration;

    /**
     * @return array
     */
    public function serialize()
    {
        return array(
            'client_configuration' => serialize($this->clientConfiguration),
        );
    }

    /**
     * @param array $config
     *
     * @return configuration
     */
    public static function unserialize(array $config)
    {
        $configuration = new static;

        if (false !== ($clientConfiguration = unserialize($config['client_configuration']))) {
            $configuration->setClientConfiguration($clientConfiguration);
        }

        return $configuration;
    }

    /**
     * @param ClientConfiguration $v
     *
     * @return $this
     */
    public function setClientConfiguration(ClientConfiguration $v = null)
    {
        $this->clientConfiguration = $v;

        return $this;
    }

    /**
     * @return ClientConfiguration
     */
    public function getClientConfiguration()
    {
        return $this->clientConfiguration;
    }
}
