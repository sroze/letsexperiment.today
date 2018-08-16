-- Experiments
SELECT e.name, c.email
FROM experiment e
INNER JOIN experiments_collaborators ec
    ON ec.experiment_uuid = e.uuid
INNER JOIN collaborator c
    ON ec.collaborator_uuid = c.uuid
