# Performance Extension [![License](https://img.shields.io/packagist/l/friends-of-behat/performance-extension.svg)](https://packagist.org/packages/friends-of-behat/performance-extension) [![Version](https://img.shields.io/packagist/v/friends-of-behat/performance-extension.svg)](https://packagist.org/packages/friends-of-behat/performance-extension) [![Build status on Linux](https://img.shields.io/travis/FriendsOfBehat/PerformanceExtension/master.svg)](http://travis-ci.org/FriendsOfBehat/PerformanceExtension) [![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/FriendsOfBehat/PerformanceExtension.svg)](https://scrutinizer-ci.com/g/FriendsOfBehat/PerformanceExtension/)
#### Suitable for PHP 5.6 only!
###### The simplest Behat extension you've ever used!

Accelerates Behat using features available only for newer PHP versions. Up to 10% faster in real-world applications. 

 - [Sylius](https://github.com/Sylius/Sylius/pull/5583) test suite execution time was reduced by ~40s / ~8% - see [the performance comparison](https://blackfire.io/profiles/compare/185a658c-0e6f-42cd-982c-2030aba45f33/graph).

## Usage

1. Install it:
    
    ```bash
    $ composer require friends-of-behat/performance-extension --dev
    ```

2. Enable it in your Behat configuration:
    
    ```yaml
    # behat.yml
    default:
        # ...
        extensions:
            FriendsOfBehat\PerformanceExtension: ~
    ```

3. Boom! :boom: Your Behat runs faster now!

## What's inside?

Right now, it's just `call_user_func_array($callable, $arguments)` replaced with `$callable(...$arguments)`.
