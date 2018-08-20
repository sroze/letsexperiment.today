Feature:
  In order to fully beneficiate from an experiment, it needs to be started
  As a user
  I want to receive a reminder that my experiment was not started

  Scenario: A reminder is not sent right after
    Given the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has been created "-1 hour"
    And the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has the collaborator "samuel.roze@gmail.com"
    When I run the command "send-emails"
    Then a check-in reminder should NOT have been sent to "samuel.roze@gmail.com"

  Scenario: A reminder is sent after 2 days
    Given the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has been created "-3 days"
    And the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has the collaborator "samuel.roze@gmail.com"
    When I run the command "send-emails"
    Then a check-in reminder should have been sent to "samuel.roze@gmail.com"
