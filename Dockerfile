FROM docker.io/alpine:latest
LABEL maintainer="dmpop@cameracode.coffee"
LABEL version="1.0"
LABEL description="Tenki container image"
RUN apk update
RUN apk add php-cli php-json php-curl php-session
COPY . /usr/src/tenki
WORKDIR /usr/src/tenki
EXPOSE 8000
CMD [ "php", "-S", "0.0.0.0:8000" ]
