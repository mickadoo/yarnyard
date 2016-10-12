FROM debian:jessie
MAINTAINER michaeldevery@gmail.com
RUN apt-get update

# required packages
RUN apt-get install -y wget git

# add php7 source
RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list \
    && wget https://www.dotdeb.org/dotdeb.gpg \
    && apt-key add dotdeb.gpg

RUN apt-get update

# php 7 and required extensions
RUN apt-get install -y php7.0-cli php7.0-bcmath php7.0-mbstring php7.0-dom php7.0-zip php7.0-mysql php7.0-sqlite

# composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# optional tools
RUN apt-get install -y curl vim mysql-client

# arguments
ARG database_host='localhost'
ARG database_user='root'
ARG database_password='root'
ARG database_driver='pdo_sqlite'
ARG oauth_client_id='local'
ARG oauth_client_secret='oauth_secret'
ARG rabbit_mq_host='localhost'
ARG rabbit_mq_uname='guest'
ARG rabbit_mq_pass='guest'
ARG elastic_host='localhost'

# environment
ENV YARNYARD_DATABASE_HOST=$database_host
ENV YARNYARD_DATABASE_USER=$database_user
ENV YARNYARD_DATABASE_PASSWORD=$database_password
ENV YARNYARD_DATABASE_DRIVER=$database_driver
ENV YARNYARD_OAUTH_CLIENT_ID=$oauth_client_id
ENV YARNYARD_OAUTH_CLIENT_SECRET=$oauth_client_secret
ENV YARNYARD_RABBIT_MQ_HOST=$rabbit_mq_host
ENV YARNYARD_RABBIT_MQ_UNAME=$rabbit_mq_uname
ENV YARNYARD_RABBIT_MQ_PASS=$rabbit_mq_pass
ENV YARNYARD_ELASTIC_HOST=$elastic_host

# xdebug
RUN apt-get install -y php7.0-xdebug
RUN echo "xdebug.remote_enable=on \n\
          xdebug.remote_host=172.17.0.1 \n\
          xdebug.remote_port=9000 \n\
          xdebug.remote_connect_back=Off \n\
          xdebug.remote_autostart=off" >> /etc/php/7.0/mods-available/xdebug.ini
RUN echo "alias phpdb='export XDEBUG_CONFIG=\"idekey=PHPSTORM\"; export PHP_IDE_CONFIG=\"serverName=docker\"; php '" >> ~/.bashrc

# ssh (for remote interpreter)
RUN apt-get install -y openssh-server
RUN mkdir /var/run/sshd \
    && chmod 0755 /var/run/sshd \
    && /usr/sbin/sshd
RUN useradd --create-home --shell /bin/bash --groups sudo php-remote \
    &&  echo "php-remote:php-remote" | chpasswd

EXPOSE 22
EXPOSE 80