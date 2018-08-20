Feature:
  In order to keep following the experimentation
  As a user
  I want to receive a reminder that I should check-in for a few experiments

  Rules for a weekly check-in:
  - I will be reminded every 7 days after I started the experiment
  - If I did check-in in the last 5 days, I will NOT receive a notification
  - I should receive only one reminder per week

  Scenario: Send a check-in reminder
    Given the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has been started "-7 days"
    And the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has the collaborator "samuel.roze@gmail.com"
    When I run the command "send-emails"
    Then a check-in reminder should have been sent to "samuel.roze@gmail.com"

  Scenario: Does not send a reminder straight after the experiment has been created
    Given the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has been started "now"
    And the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has the collaborator "samuel.roze@gmail.com"
    When I run the command "send-emails"
    Then a check-in reminder should NOT have been sent to "samuel.roze@gmail.com"
