#!/bin/sh

create() {
    docker run -d --name ls                           \
        -p 4566:4566                                  \
        -v /tmp/localstack:/tmp/localstack            \
        -v /var/run/docker.sock:/var/run/docker.sock  \
        -e SERVICES=s3                                \
        -e TEST_AWS_ACCOUNT_ID=123456789012           \
        -e DOCKER_HOST=unix:///var/run/docker.sock    \
        localstack/localstack:latest
}

start() {
    docker container start ls
}

stop() {
    docker container stop ls
}

restart() {
    stop
    start
}

remove() {
    docker stop ls
    docker rm ls
}

version() {
    local container=`docker ps -q -f name=ls`

    if [ -n "$container" ]; then
        docker exec ls python3 bin/localstack --version
    else
        echo "Start the localstack container"
    fi
}

command=${1:-restart}
$command
exit 0
