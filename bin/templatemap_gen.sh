#!/bin/sh

DIR="$( cd "$( dirname "$0" )" && pwd )"
projectDir=`dirname $DIR`
moduleDirs=`ls $projectDir/module`

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
