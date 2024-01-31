FROM mysql:8.0.24

ENV DEBIAN_FRONTEND noninteractive

RUN set -xe \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys b7b3b788a8d3785c \
    && apt-get update \
    && apt-get install -y locales \
    && rm -rf /var/lib/apt/lists/* \
    && echo 'en_IN.UTF-8 UTF-8' > /etc/locale.gen \
    && locale-gen en_IN.UTF-8

ENV LANG en_IN.UTF-8
ENV LC_ALL en_IN.UTF-8

COPY character-set.cnf /etc/mysql/conf.d/
