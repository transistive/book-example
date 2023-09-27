# List all users with their comments
SELECT username, comment, c.created_at, c.updated_at
FROM comments c
         JOIN test.users u on c.user_id = u.id
GROUP BY username
LIMIT 20;

# list all articles
SELECT *
FROM articles a
         JOIN articles oa on a.id = oa.id
GROUP BY a.title
LIMIT 10;

# list all tags on articles
SELECT *
FROM articles
         JOIN article_tags at on articles.id = at.article_id
         JOIN tags t on at.tag_id = t.id
GROUP BY t.tag
LIMIT 10;

# list all categories and their corresponding resources
SELECT pc.category       AS category_name,
       pc.resource_table AS resource,
       a.id              AS resource_id,
       a.title           as resource_value
FROM articles a,
     polymorphic_categories pc
WHERE pc.resource_table = 'articles'
  AND a.id = pc.resource_id
UNION
SELECT pc.category       AS category_name,
       pc.resource_table AS resource,
       c.id              AS resource_id,
       c.comment         AS resource_value
FROM comments c,
     polymorphic_categories pc
WHERE pc.resource_table = 'comments'
  AND c.id = pc.resource_id
UNION
SELECT pc.category       AS category_name,
       pc.resource_table AS resource,
       u.id              AS resource_id,
       u.username        AS resource_value
FROM users u,
     polymorphic_categories pc
WHERE pc.resource_table = 'users'
  AND u.id = pc.resource_id
UNION
SELECT pc.category       AS category_name,
       pc.resource_table AS resource,
       t.id              AS resource_id,
       t.tag             AS resource_value
FROM tags t,
     polymorphic_categories pc
WHERE pc.resource_table = 'tags'
  AND t.id = pc.resource_id
ORDER BY category_name, resource, resource_id
LIMIT 10;

# provide an overview of the depth of each article
# in the hierarchy
WITH RECURSIVE childQuery AS (
    SELECT id, parent_id, title, 1 AS level
    FROM articles
    WHERE parent_id IS NULL
    UNION ALL
    SELECT a.id, a.parent_id, a.title, level + 1
    FROM articles a
             JOIN childQuery cq ON a.parent_id = cq.id
)
SELECT title, id, level FROM childQuery
LIMIT 10;