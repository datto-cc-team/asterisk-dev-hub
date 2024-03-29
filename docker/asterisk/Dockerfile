FROM ubuntu:18.04

# set non-interactive
ENV DEBIAN_FRONTEND noninteractive

# allow use of nano
ENV TERM xterm

# set the appropriate timezone
RUN echo "Etc/UTC" > /etc/timezone

# install software-properties-common for access to add-apt-repository command
RUN apt update
RUN apt install -yq --force-yes software-properties-common

# add asterisk 16 repo
RUN add-apt-repository ppa:jan-hoffmann/asterisk16
RUN apt update

## update/upgrade
## install dependencies
RUN apt -yq --force-yes install \
    asterisk php-cli nano vim rsync unixodbc wget curl git unzip

## copy executable to image
COPY ./docker/asterisk/build/init.sh /init.sh
COPY ./docker/asterisk/build/odbc/odbc.ini /etc/odbc.ini
COPY ./docker/asterisk/build/odbc/odbcinst.ini /etc/odbcinst.ini
COPY ./docker/asterisk/build/mysql-odbc-connector.sh /mysql-odbc-connector.sh

# allow asterisk-configs to be mountable
VOLUME "/instance-configs/"
VOLUME "/shared-configs/"

# run script
CMD ["./init.sh"]
