#!/bin/bash

docker stop wp
docker container rm wp
docker build -t wp-alpine .
docker run -d -p 8080:80 -p 9000:9000 --name wp wp-alpine
