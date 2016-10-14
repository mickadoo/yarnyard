FROM mickadoo/php7
MAINTAINER michaeldevery@gmail.com
RUN apt-get update

# default environment variables
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
