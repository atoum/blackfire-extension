# atoum/blackfire-extension [![Build Status](https://travis-ci.org/atoum/blackfire-extension.svg?branch=master)](https://travis-ci.org/atoum/blackfire-extension)

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
    public function testExemple()
    {
        $this
            ->blackfire
                ->assert('main.wall_time < 2s', "Temps d'execution")
                ->profile(function() {
                    sleep(4); //just to make the test fail

                    //some code to profile
                    // ...

                    //you also can run atoum assertions inside this callable
                    //but beware, atoum's logic will also be profiled.
                    $this->boolean(true)->isTrue();
                })
        ;
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

Enable and configure the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$runner
    ->getExtension(mageekguy\atoum\blackfire\extension::class)
    ->setClientConfiguration(new \Blackfire\ClientConfiguration($_ENV['BLACKFIRE_CLIENT_ID'], $_ENV['BLACKFIRE_CLIENT_TOKEN']))
    ->addToRunner($runner)
;
```

## Other examples

### Define custom metrics

```php
$this
    ->blackfire
        ->defineMetric(new \Blackfire\Profile\Metric("example_method_calls", "=Example::method"))
        ->assert("metrics.example_method_calls.count < 10")
        ->profile(function() {
            $testedClass = new TestedClass();
            for ($i = 0; $i < 8; $i++) {
                $testedClass->method();
            }
        })
;
```

You can learn more about this on the [custom metric](https://blackfire.io/docs/reference-guide/metrics#custom-metrics)'s section of Blackfire documentation.

### Pass your own profile configuration

```php
$this
    ->given(
        $profileConfiguration = new \Blackfire\Profile\Configuration(),
        $profileConfiguration->setTitle('Profile configuration title'),
        $testedClass = new TestedClass()
    )
    ->blackfire
        ->setConfiguration($profileConfiguration)
        ->assert("main.peak_memory < 10mb")
        ->profile(function() use ($testedClass) {
            $testedClass->method();
        })
;
```

You can learn more about this on the [profile basic configurable](https://blackfire.io/docs/reference-guide/php-sdk#profile-basic-configuration)'s section of Blackfire documentation.

### Pass a custom client

You can either pass the blackfire client in the `.atoum.php` config file (when loading the extension). In that case the client will be used in all the blackfire assertions. You also can load/overload it in the `blackfire` assert. For example:

```php
$this
    ->given(
      $config = new \Blackfire\ClientConfiguration($_ENV['BLACKFIRE_CLIENT_ID'], $_ENV['BLACKFIRE_CLIENT_TOKEN']);
      $client = new \Blackfire\Client($config);
    )
    ->blackfire(client)
        ->assert('main.wall_time < 2s')
        ->profile(function() {
            //code to profile
        })
;
```


## Test filtering

To avoid running the test if the blackfire extension is not loaded, you can use the `@extensions` annotation.

```php
    /**
     * @extensions blackfire
     */
    public function testExemple()
    {
        $this
            ->blackfire($this->getBlackfireClient())
                ->defineMetric(new \Blackfire\Profile\Metric("example_method_calls", "=Example::method"))
                ->assert("metrics.example_method_calls.count < 10")
                ->profile(function() {
                    $testedClass = new TestedClass();
                    for ($i = 0; $i < 8; $i++) {
                        $testedClass->method();
                    }
                })
        ;
    }
```

You can add this annotation on both the test method or the test class.

Then, when running the test, the classes/methods with this annotation will be skipped if the extension is not present/loaded:

```
Success (1 test, 0/1 method, 0 void method, 1 skipped method, 0 assertion)!
> There is 1 skipped method:
=> Tests\Units\Example::testExemple(): PHP extension 'blackfire' is not loaded
```

You also can use [atoum's tags](http://docs.atoum.org/en/latest/launch_test.html#tags) and the [ruler extension](https://github.com/atoum/ruler-extension) to only run the blackfire tests.


## Links

* [Blackfire.io](https://blackfire.io)
* [Blackfire's documentation](https://blackfire.io/docs/introduction)
* [Blackfire PHP-SDK](https://github.com/blackfireio/php-sdk)
* [atoum](http://atoum.org)
* [atoum's documentation](http://docs.atoum.org)


## Licence

blackfire-extension is released under the MIT License. See the bundled LICENSE file for details.

![atoum](http://atoum.org/images/logo/atoum.png)
