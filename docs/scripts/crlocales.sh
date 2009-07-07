#!/bin/bash

#
# creat all locales files for translation with Gettext
#
LOCALES_DIR="./locale/";
LOCALES_MESSAGES_DIR="/LC_MESSAGES/";
domain=$1;

for locale in cs_CZ en_US de_DE; do
#localeDir=$LOCALES_DIR$locale;
	if [ -d "$LOCALES_DIR$locale" ]; then
		if [ -f "$LOCALES_DIR$locale$LOCALES_MESSAGES_DIR$domain.po" ]; then
			xgettext -j --from-code=UTF-8 *.php --keyword=_m -d $domain -p $LOCALES_DIR$locale$LOCALES_MESSAGES_DIR;
			echo "locale $locale doplněno";
		else
			xgettext --from-code=UTF-8 *.php --keyword=_m -d $domain -p $LOCALES_DIR$locale$LOCALES_MESSAGES_DIR;
			echo "locale $locale vytvořeno";
		fi
	else
		echo "locale $locale neexistuje";
	fi
done

#xgettext <-j> --from-code=UTF-8 *.php -d <doména> -p locale/<jazyk (cs_CZ, en_US, ...)>/LC_MESSAGES/
