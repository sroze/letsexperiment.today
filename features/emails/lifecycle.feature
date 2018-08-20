Feature:
  In order to keep all the collaborators aware of what is going on with their experiments
  I want to send emails for important lifecycle events of an experiment

  Scenario: A experiment is started
    Given there is an experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808"
    And the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" has the collaborator "samuel.roze@gmail.com"
    When I start the experiment "EBFED3F1-0FE6-4015-BB47-9AF0EA02A808" as collaborator "samuel.roze@gmail.com" for "+2 weeks"
    Then a check-in reminder should have been sent to "samuel.roze@gmail.com"
