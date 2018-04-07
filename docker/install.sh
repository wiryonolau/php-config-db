#!/bin/bash

#Add repo
rpm --import https://dl.fedoraproject.org/pub/epel/RPM-GPG-KEY-EPEL-7
rpm --import https://repo.webtatic.com/yum/RPM-GPG-KEY-webtatic-el7
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

#Update if require, optional for development environment 
yum update -y

#Install php
yum install -y php56w php56w-opcache php56w-mcrypt php56w-pdo php56w-bcmath php56w-gd php56w-intl php56w-ldap php56w-mbstring php56w-pecl-memcached php56w-xml php56w-mysqlnd

#Install git
yum install -y git

#Install composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
chmod 755 /usr/local/bin/composer

#Install gosu for non-root user
yum install -y ca-certificates curl
gpg --keyserver ha.pool.sks-keyservers.net --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4
curl -o /usr/local/bin/gosu -SL "https://github.com/tianon/gosu/releases/download/1.10/gosu-amd64"
curl -o /usr/local/bin/gosu.asc -SL "https://github.com/tianon/gosu/releases/download/1.10/gosu-amd64.asc"
gpg --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu
chmod +x /usr/local/bin/gosu

#Clean yum cache
yum clean all
rm -rf /var/cache/yum/*
