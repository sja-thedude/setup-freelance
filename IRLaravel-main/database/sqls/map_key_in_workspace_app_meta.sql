UPDATE workspace_app_meta
SET `key` = CASE
              WHEN name = 'Reserveren' THEN 'reserve'
              WHEN name = 'Reviews' THEN 'reviews'
              WHEN name = 'Route' THEN 'route'
              WHEN name = 'Jobs' THEN 'jobs'
              WHEN name = 'Recent' THEN 'recent'
              WHEN name = 'Favorieten' THEN 'favorites'
              WHEN name = 'Account' THEN 'account'
              WHEN name = 'Deel' THEN 'share'
              WHEN name = 'Klantkaart' THEN 'loyalty'
              WHEN name = 'Menukaart' THEN 'menu'
    END