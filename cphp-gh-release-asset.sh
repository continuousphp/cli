#!/bin/bash

sign()
{
    gpg --detach-sign $1
    gpg --verify $1.sig $1
}

TAG=$(shell echo ${CPHP_GIT_REF} | tail -c +11)
PHAR_NAME="continuousphp-$TAG.phar"

if [ -z ${CONTINUOUSPHP} ];
then
    echo "Your are not on ContinuousPHP environment"
    exit 1
fi

sign $PHAR_NAME

upload_url=`curl -sS -H "Authorization: token ${GITHUB_TOKEN}" https://api.github.com/repos/continuousphp/cli/releases/tags/$TAG | jq --compact-output '.upload_url' | sed 's/{?name,label}//g' | sed 's/"//g'`

echo "Attach phar to github release: $PHAR_NAME"
echo "Upload to $upload_url"

curl -sS -H "Authorization: token ${GITHUB_TOKEN}" -H "Content-Type: application/octet-stream" --upload-file $PHAR_NAME "$upload_url?name=continuousphpcli.phar"
curl -sS -H "Authorization: token ${GITHUB_TOKEN}" -H "Content-Type: application/octet-stream" --upload-file "$PHAR_NAME.sig" "$upload_url?name=continuousphpcli.sig"

