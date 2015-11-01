#!/bin/sh

DIR="$( cd "$( dirname "$0" )" && pwd )"
projectDir=`dirname $DIR`
moduleDirs=`ls $projectDir/module`
while true; do
    read -p "Do you wish to delete old template_map file?" yn
    case $yn in
        [Yy]* ) delMap=true; break;;
        [Nn]* ) delMap=false;break;;
        * ) echo "Please answer yes or no.";;
    esac
done

for dir in $moduleDirs
do
    if [ -d "$projectDir/module/$dir/view" ]; then
        cd "$projectDir/module/$dir"
        echo `pwd`
        sh -c "$projectDir/vendor/bin/templatemap_generator.php"
    else
        if [ $delMap ]; then
            rm -f "$projectDir/module/$dir/template_map.php"
        fi
        echo "$projectDir/module/$dir has been skipped"
    fi
done;

exit
