UPDATE users
SET
    email_tmp = email,
    email = CONCAT('removed-', id, '@itsready.be')
WHERE email NOT LIKE 'removed-%'
  AND deleted_at IS NOT NULL;