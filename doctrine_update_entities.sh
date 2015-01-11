if [ "$1" == "" ]
then
  echo "$(tput setaf 6)No command-line parameter given; Using standard \
executable \"php\"$(tput setaf 7)"
fi

phpExec=${1:-"php"};

cd code/include/3rdParty/doctrine-orm
$phpExec bin/doctrine orm:generate-entities ../../models/Entities

