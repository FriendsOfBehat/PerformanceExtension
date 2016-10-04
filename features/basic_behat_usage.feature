Feature: Basic Behat usage
    In order to test whether Behat still works as expected
    As a PerformanceExtension developer
    I want to be sure it is

    Background:
        Given a Behat configuration containing:
        """
        default:
            extensions:
                FriendsOfBehat\PerformanceExtension: ~
        """

    Scenario: Regular calls with no arguments
        Given a context file "features/bootstrap/FeatureContext.php" containing:
        """
        <?php

        use Behat\Behat\Context\Context;

        class FeatureContext implements Context
        {
            /** @Then it passes */
            public function itPasses() {}
        }
        """
        And a feature file "features/regular_calls.feature" containing:
        """
            Feature: Regular calls with no arguments

                Scenario: Passing scenario
                    Then it passes
        """
        When I run Behat
        Then it should pass

    Scenario: Making sure transformers receiving associative arrays still works
        Given a context file "features/bootstrap/FeatureContext.php" containing:
        """
        <?php

        use Behat\Behat\Context\Context;

        class FeatureContext implements Context
        {
            /**
             * @Then I transform :something
             */
            public function iTransform($something)
            {
                // This Turnip pattern is transformed into:
                //   /^you take my ["']?(?P<something>(?<=")[^"]*(?=")|(?<=')[^']*(?=')|\-?[\w\.\,]+)['"]?$/i
                // Then the transformer is trying to be called with arguments:
                //   ['something' => 'raw_value']
                // But our custom CallHandler tries to unpack that array with `...`, which results in:
                //   Fatal error: Cannot unpack array with string keys
                // So this scenario checks whether `array_values()` is called on arguments before executing the call.
            }

            /**
             * @Transform :something
             */
            public function transformSomething($something)
            {
                return 'value';
            }
        }
        """
        And a feature file "features/transformer_call.feature" containing:
        """
            Feature: Transformer call

                Scenario: Transformer call
                    Then I transform "something"
        """
        When I run Behat
        Then it should pass
