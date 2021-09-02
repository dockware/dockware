{% extends "variants/dev/Dockerfile.sw5.sh.twig" %}

{# 5.7.0 does not have DEMO data, so we overwrite it here #}

{% block shopware %}
RUN rm -rf /var/www/html/* \
    && wget {{ shopware.download_url }} -qq -O /var/www/shopware.zip \
    && sudo -u www-data unzip -q /var/www/shopware.zip -d /var/www/html \
    && rm -rf /var/www/shopware.zip

{% include 'template/components/shopware/shopware5/install.sh.twig' with {'db_host': db.host, 'db_user' : db.user, 'db_database' : db.database, 'db_pwd' : db.pwd, 'demo_data' : false } %}

RUN rm -rf /var/www/html/config.php
COPY ./assets/shopware5/config.php /var/www/html/

{% endblock %}