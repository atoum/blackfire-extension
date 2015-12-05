# atoum/blackfire-extension

blackfire-extension allows you to use blackfire assertions inside [atoum](https://github.com/atoum/atoum).

The Blackfire PHP-SDK has a built-in [PHPUnit integration](https://blackfire.io/docs/integrations/phpunit). This extension does the same, but for atoum.

## Example

Let's take this example.

```php
namespace Tests\Units;

use Example as TestedClass;

use atoum;

class Example extends atoum
{
    protected $blackfire;

    public function testExemple()
    {
        $config = new \Blackfire\Profile\Configuration();
        $config
            ->assert('main.wall_time < 2s', "Temps d'execution")
        ;

        $callback = function() {

            sleep(4);

            //some code and/or atoum assertions
            $this->boolean(true)->isTrue();
        };

        $this
            ->blackfireProfile($this->getBlackfireClient(), $callback, $config)
            ->matchesAssertions()
        ;
    }

    private function getBlackfireClient()
    {
        if (null === $this->blackfire) {
            $config = new \Blackfire\ClientConfiguration($_ENV['BLACKFIRE_CLIENT_ID'], $_ENV['BLACKFIRE_CLIENT_TOKEN']);
            $this->blackfire = new \Blackfire\Client($config);
        }

        return $this->blackfire;
    }
}
```

When running this test, the callback will be automatically instrumented and execute on Blackfire the assertions defined by the Configuration. If they fail, an atoum error will be displayed.The above example will have this output : 

![Instrumentation result](doc/screenshot.png)


## Install it

Install extension using [composer](https://getcomposer.org):

```
composer require --dev atoum/blackfire-extension
```

Enable the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$runner->addExtension(new \mageekguy\atoum\blackfire\extension());
```


