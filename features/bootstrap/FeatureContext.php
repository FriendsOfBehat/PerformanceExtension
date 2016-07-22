<?php

use Behat\Behat\Context\Context;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FeatureContext implements Context
{
    /**
     * @Given I left alone
     * @Given my mind was blank
     * @Given I needed time to think
     * @When I get the memories from my mind
     * @Then the evil face should twist my mind
     * @Then it should bring me to despair
     * @Then I should take yours too
     */
    public function doNothing()
    {

    }

    /**
     * @When you take my :life
     */
    public function doNothingButWithAnArgument($life)
    {
        // This Turnip pattern is transformed into:
        //   /^you take my ["']?(?P<life>(?<=")[^"]*(?=")|(?<=')[^']*(?=')|\-?[\w\.\,]+)['"]?$/i
        // Then the transformer is trying to be called with arguments:
        //   ['life' => 'life']
        // But our custom CallHandler tries to unpack that array with `...`, which results in:
        //   Fatal error: Cannot unpack array with string keys
        // So this scenario checks whether `array_values()` is called on arguments before executing the call.
    }

    /**
     * @Transform :life
     */
    public function transformLifeToDeath($life)
    {
        return 'death';
    }
}
