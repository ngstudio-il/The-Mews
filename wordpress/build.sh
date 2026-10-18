#!/usr/bin/env bash
# Builds the installable plugin zip: copies the site's shared assets into the plugin and zips it.
# Usage: wordpress/build.sh   ->  wordpress/dist/the-mews-landing.zip
set -euo pipefail
cd "$(dirname "$0")/.."

PLUGIN=wordpress/the-mews-landing
rm -rf "$PLUGIN/assets"
mkdir -p "$PLUGIN/assets/css" "$PLUGIN/assets/js" "$PLUGIN/assets/img" "$PLUGIN/assets/video"

cp assets/css/styles.css "$PLUGIN/assets/css/"
cp assets/js/main.js assets/js/a11y.js "$PLUGIN/assets/js/"
cp assets/img/*.svg assets/img/*.jpg assets/img/*.png "$PLUGIN/assets/img/"
# MP4 only: every current browser plays it, and it keeps the zip small enough to upload
cp assets/video/hero-desktop.mp4 assets/video/hero-mobile.mp4 "$PLUGIN/assets/video/"

mkdir -p wordpress/dist
rm -f wordpress/dist/the-mews-landing.zip
(cd wordpress && zip -qr dist/the-mews-landing.zip the-mews-landing -x '*.DS_Store')
ls -lh wordpress/dist/the-mews-landing.zip
