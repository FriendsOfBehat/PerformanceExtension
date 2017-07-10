Feature: Not crashing Behat

    Scenario: Not crashing Behat
        Given a Behat configuration containing:
        """
        default:
            extensions:
                FriendsOfBehat\PerformanceExtension: ~
        """
        And a feature file with passing scenario
        When I run Behat
        Then it should pass
