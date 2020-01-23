#!/bin/bash
docker exec -ti nextcloud sudo -u abc /config/www/nextcloud/occ config:system:set trusted_domains 1 --value=eweiss.servebeer.com
