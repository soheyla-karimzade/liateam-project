docker-compose.yml FROM ubuntu:latest
LABEL authors="soheyla"

ENTRYPOINT ["top", "-b"]
