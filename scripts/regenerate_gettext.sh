#!/bin/bash
DOMAIN=nakweb
CATALOG_FILENAME=${DOMAIN}.po
BINARY_FILENAME=${DOMAIN}.mo

WEBAPP_DIR=../files/nak/webapp
LOCALE_DIR=${WEBAPP_DIR}/locale
ENGLISH_DIR=${WEBAPP_DIR}/locale/en/LC_MESSAGES

ENGLISH_CATALOG=${ENGLISH_DIR}/${CATALOG_FILENAME}
ENGLISH_BINARY=${ENGLISH_DIR}/${BINARY_FILENAME}
NEW_ENGLISH_CATALOG=${ENGLISH_DIR}/new_catalog.po

TRANSIFEX_PROJECT_NAME=netaidkit
TRANSIFEX_WEBAPP_RESOURCE_NAME=nakwebpo

function stage_lang() {
    git add $LOCALE_DIR/$1/LC_MESSAGES/${CATALOG_FILENAME} \
        $LOCALE_DIR/$1/LC_MESSAGES/${BINARY_FILENAME}
}

function stage_all() {
    for langdir in ${LOCALE_DIR}/*
    do
        lang=$(basename $langdir)
        stage_lang $lang
    done
}

function scan_codebase() {
    xgettext -c -L PHP -n $(find $WEBAPP_DIR -name \*php) \
        $(find $WEBAPP_DIR -name \*phtml) $(find $WEBAPP_DIR -name \*pjs) \
        -o $NEW_ENGLISH_CATALOG

    # update English catalog
    msgmerge -U -N $ENGLISH_CATALOG $NEW_ENGLISH_CATALOG
    rm $NEW_ENGLISH_CATALOG
    msgen $ENGLISH_CATALOG -o $ENGLISH_CATALOG
    msgfmt $ENGLISH_CATALOG -o $ENGLISH_BINARY
}

# Sets up English strings so they point back to themselves until properly
# translated.
function add_placeholders() {
    for langdir in ${LOCALE_DIR}/*
    do
        messagedir=${langdir}/LC_MESSAGES
        lang_catalog=${messagedir}/$CATALOG_FILENAME
        lang_binary=${messagedir}/$BINARY_FILENAME

        if [ "$messagedir" == "$ENGLISH_DIR" ]; then
            continue
        fi

        echo Processing $messagedir
        msgmerge -U -N $lang_catalog $ENGLISH_CATALOG
        msgfmt $lang_catalog -o $lang_binary
    done
}

function transifex_download_translations() {
    for langdir in ${LOCALE_DIR}/*
    do
        lang=$(basename $langdir)
        messagedir=${langdir}/LC_MESSAGES
        api_output=${messagedir}/transifex_query
        downloaded_catalog=${messagedir}/transifex_$CATALOG_FILENAME
        catalog=${messagedir}/$CATALOG_FILENAME
        binary_catalog=${messagedir}/$BINARY_FILENAME

        if [ "$messagedir" == "$ENGLISH_DIR" ]; then
            continue
        fi

        echo Downloading translation for $lang.

        curl -f -s -L --user $1:$2 -X GET \
            https://www.transifex.com/api/2/project/$TRANSIFEX_PROJECT_NAME/resource/$TRANSIFEX_WEBAPP_RESOURCE_NAME/translation/$lang \
            -o $api_output

        if [ ! -f $api_output ]; then
            >&2 echo "Couldn't get $lang translation from Transifex API"
            continue
        fi

        jq -r -e .content $api_output > $downloaded_catalog

        if [ "$?" != "0" ]; then
            >&2 echo "Couldn't get $lang translation from Transifex API"
            continue
        fi

        echo Regenerating binary translation catalog for $lang.
        mv $downloaded_catalog $catalog
        msgfmt $catalog -o $binary_catalog

        rm -f $api_output

        echo Adding $lang files to git staging area.
    done
}

function transifex_upload_english_catalog() {
    curl -i -L --user $1:$2 -F file=@$ENGLISH_CATALOG -X PUT \
        -H "Content-Type: multipart/form-data" \
        https://www.transifex.com/api/2/project/$TRANSIFEX_PROJECT_NAME/resource/$TRANSIFEX_WEBAPP_RESOURCE_NAME/content/
}

function add_language() {
    langdir=$LOCALE_DIR/$1/LC_MESSAGES
    catalog=$langdir/$CATALOG_FILENAME
    binary=$langdir/$BINARY_FILENAME

    mkdir -p $langdir
    cp $ENGLISH_CATALOG $catalog
    msgfmt $catalog -o $binary
}

function usage() {
    echo -e "NetAidKit gettext tool"
    echo -e "rescan"
    echo -e "\tscan codebase and update English catalog and translation placeholders"
    echo -e "download <transifex user> <transifex password>"
    echo -e "\tdownloads translations from Transifex for languages present in locale/ directory"
    echo -e "upload <transifex user> <transifex password>"
    echo -e "\tuploads english catalog to Transifex; remember to rescan first"
    echo -e "add-lang"
    echo -e "\tcreates directory subtree for another language under locale/"
    echo -e "stage-lang <language two-letter code>"
    echo -e "\tmoves language files to git staging area"
    echo -e "stage-all"
    echo -e "\tmoves all languages' files to git staging area"
    echo -e "usage"
    echo -e "\tthis message"
}

function main() {
    case $1 in
        rescan)
            scan_codebase
            add_placeholders
            ;;
        download)
            transifex_download_translations $2 $3
            ;;
        upload)
            transifex_upload_english_catalog $2 $3
            ;;
        add-lang)
            add_language $2
            ;;
        stage-lang)
            stage_lang $2
            ;;
        stage-all)
            stage_all
            ;;
        usage)
            usage
            ;;
        *)
            usage
            ;;
    esac
}

main $@
