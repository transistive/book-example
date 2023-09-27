# Graphing Relational Database Models


Installation instructions:

```sh
# clone the repository
git clone git@github.com transistive/book-example.git
# change to project root directory
cd book-example
# copy the default environment (works out of the box with the current docker compose file)
cp .env.example .env

# install PHP all libraries
docker compose run php composer install
# migrate the SQL schema
docker compose run php vendor/bin/phinx migrate 
# generate random MariaDB dataset
docker compose run php vendor/bin/phinx seed:run
```

Run the migration script:

```sh
docker compose run php php migrate_to_neo4j.php 
```