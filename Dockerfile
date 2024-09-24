FROM docker.io/debian
LABEL maintainer="dmpop@cameracode.coffee"
LABEL version="0.1"
LABEL description="Tenki container image"
RUN apt update
RUN apt install -y php-cli php-json
COPY . /usr/src/tenki
WORKDIR /usr/src/tenki
EXPOSE 8080
CMD [ "php", "-S", "0.0.0.0:8080" ]
