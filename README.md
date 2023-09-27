# Graphing Relational Database Models


Installation instructions:

```sh
git clone git@github.com transistive/book-example.git
cd book-example

# install PHP all libraries
docker compose run php composer install

# set up MariaDB and Neo4J
docker compose up -d
# migrate the SQL schema
docker compose run php vendor/bin/phinx migrate 
# migrate the SQL schema
docker compose run php vendor/bin/phinx migrate 
# generate random MariaDB dataset
docker compose run php vendor/bin/phinx seed:run
```

Run the migration script:

```sh
docker compose run php migrate_to_neo4j.php 
```