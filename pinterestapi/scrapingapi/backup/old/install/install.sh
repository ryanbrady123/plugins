#!/bin/sh

if [ ! -d "$1" ]; then
    mkdir $1
fi

if [ -d "$1/pictures" ]; then
	mv $1/pictures $1/pictures_

fi

if [ -d "$1/config" ]; then
	mv $1/config $1/config_

fi

if [ -d "$1/cookies" ]; then
        mv $1/cookies $1/cookies_

fi

if [ -d "$1/cached_sources" ]; then
        mv $1/cached_sources $1/cached_sources_

fi

if [ -d "$1/scheduled_templates" ]; then
        mv $1/scheduled_templates $1/scheduled_templates_

fi


unzip -o $2 -d $1

if [ -d "$1/pictures_" ]; then
    rm -rf $1/pictures
	mv $1/pictures_ $1/pictures

fi

if [ -d "$1/config_" ]; then
    rm -rf $1/config
	mv $1/config_ $1/config

fi

if [ -d "$1/cookies_" ]; then
    rm -rf $1/cookies
	mv $1/cookies_ $1/cookies

fi

if [ -d "$1/cached_sources_" ]; then
    rm -rf $1/cached_sources
        mv $1/cached_sources_ $1/cached_sources

fi

if [ -d "$1/scheduled_templates_" ]; then
    rm -rf $1/scheduled_templates
        mv $1/scheduled_templates_ $1/scheduled_templates

fi


if [ ! -d "$1/pictures" ]; then
    mkdir $1/pictures
	chmod 0777 $1/pictures
fi

