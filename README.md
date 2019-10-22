## Asterisk Development Hub

#### Purpose

The purpose of this repository is to imitate a distributed Asterisk environment locally for development purposes.
The docker-compose file includes two locations (A and B) as well as a failover location (A secondary).
Multiple files and directories are watched for changes which automatically reloads Asterisk for convienence.
The container startup logic in maintained in `shared/run.php` and is mounted to the containers as a volume.
This approach allows a developer to modify the instance startup without rebuilding the underlying image.

#### What is included

This environment is composed of:
- 3 Asterisk instances
- 1 MySQL instance
- 1 Adminer instance (easily connect and view MySQL data)

#### Layout
- The `docker` directory contains setup instructions used by `docker-compose`.
Users should not need to modify anything in this directory.
- The `instance` directory contains instance specific information.
It is currently configured to hold Asterisk configuration files and will override and duplicate config files in the `shared` directory.
- The `shared` directory contains files and directories shared across multiple Asterisk instances.
It is currently configured for Asterisk configuration files, AGI scripts, and SSL certs.

#### How to use
This documentation assumes you already have docker and docker-compose installed.  
To setup the dev environment:
1. clone this repo
1. run `docker-compose up -d`

---
Tailing the container logs will provide information on what type of files have been modified and when Asterisk is reloaded

Example: `docker logs -f asterisk-location-a` 

---
Connect to the container shell to view instance information and connect to the Asterisk CLI

Example: `docker exec -it asterisk-location-a bash`

---
Remove a container with `docker-compose down`.  Add `-v` flag if volumes should also be removed.

If development on the local machine results in, multiple failed docker image builds, orphaned containers, or an overall unstable environment.
Just nuke it with `./nukeDocker.sh` (warning:his will destroy all docker container/volumes/images on the host).
