-- Experiments overview
SELECT 
  e.uuid, 
  e.name, 
  (SELECT COUNT(*) FROM experiments_collaborators ec WHERE ec.experiment_uuid = e.uuid) as number_of_collaborators, 
  (SELECT COUNT(*) FROM expected_outcome eo WHERE eo.experiment_uuid = e.uuid) as number_of_outcomes, 
  (SELECT COUNT(*) FROM check_in ci WHERE ci.experiment_uuid = e.uuid) as number_of_checkins
FROM experiment e

-- Experiments list
SELECT e.uuid, e.name, c.email
FROM experiment e
INNER JOIN experiments_collaborators ec
    ON ec.experiment_uuid = e.uuid
INNER JOIN collaborator c
    ON ec.collaborator_uuid = c.uuid

-- On-going experiments
SELECT COUNT(*)
FROM experiment e
WHERE period_end IS NOT NULL AND period_end > NOW()

