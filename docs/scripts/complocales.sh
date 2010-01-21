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
			msgfmt $LOCALES_DIR$locale$LOCALES_MESSAGES_DIR$domain.po -o $LOCALES_DIR$locale$LOCALES_MESSAGES_DIR$domain.mo -v -c
			echo "locale $locale zkompilováno";
		else
			echo "locale $locale nebylo vytvořeno -- nezkompilováno";
		fi
	else
		echo "locale $locale neexistuje";
	fi
done
