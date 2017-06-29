#!/bin/bash

sign()
{
    gpg --detach-sign $1
    gpg --verify $1.sig $1
}

TAG=`echo ${CPHP_GIT_REF} | tail -c +11`
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

curl -sS -H "Authorization: token ${GITHUB_TOKEN}" -H "Content-Type: application/octet-stream" --upload-file $PHAR_NAME "$upload_url?name=continuousphp.phar"
curl -sS -H "Authorization: token ${GITHUB_TOKEN}" -H "Content-Type: application/octet-stream" --upload-file "$PHAR_NAME.sig" "$upload_url?name=continuousphp.sig"

rm -rf .git
mkdocs build -d doc_dist

git clone "https://${GITHUB_TOKEN}@github.com/continuousphp/cli.git" cli-site
cd cli-site
git checkout gh-pages
rm -rf doc
mv ../doc_dist doc

php -r '$x = json_decode(file_get_contents("manifest.json"), true); $x["'$TAG'"] = ["name"=>"continuousphp.phar","sha1"=>sha1_file("../'$PHAR_NAME'"),"url"=>"https://github.com/continuousphp/cli/releases/download/'$TAG'/continuousphp.phar","version"=>substr("'$TAG'",1)]; file_put_contents("manifest.json", json_encode($x)); print_r($x);'

git config user.email "info@continuousphp.com"
git config user.name "${CPHP_BUILT_BY}"

git add -A doc
git add manifest.json

git commit -m "Update doc to tag $TAG"
git push origin gh-pages
