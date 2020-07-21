ReadMeMFTF (recommendations for running tests related to Reward Points extension).

    66 Reward Points specific tests, grouped by purpose, for greater convenience.

        Tests group: Rewards
        Runs all tests.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group Rewards -r

        Tests group: RewardsConfiguration
        Runs tests related to extension configuration.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsConfiguration -r

        Tests group: RewardsFunctional
        Runs tests related to extension's core functions.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsFunctional -r

        Tests group: RewardsEarning
        Runs tests related to extension reward points earning.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsEarning -r

        Tests group: RewardsSpending
        Runs tests related to extension reward points spending.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsSpending -r

        Tests group: RewardsHighlight
        Runs tests related to extension reward points highlight.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsHighlight -r

        Tests group: RewardsFunctionalRules
        Runs tests related to extension reward points rules.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsFunctionalRules -r

        Tests group: RewardsGrid
        Runs tests related to extension reward points rules.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group RewardsGrid -r
