MATCH (c:Comment) <- [:COMMENTED] - (u:User)
RETURN c, u
  LIMIT 20;


MATCH (a:Article) - [HAS_PARENT] -> (oa:Article)
RETURN a, oa
  LIMIT 10;

MATCH (a:Article) <- [:TAGS] - (t:Tag)
RETURN a, t
  LIMIT 10

MATCH (c:Category) - [:CATEGORIZES] -> (r)
RETURN c, r
  LIMIT 10




