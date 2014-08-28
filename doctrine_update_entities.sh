if [ "$1" == "" ]
then
  echo "$(tput setaf 6)No command-line parameter given; Using standard \
executable \"php\"$(tput setaf 7)"
  $1 = "php";
fi


cd code/include/3rdParty/doctrine-orm
$1 bin/doctrine orm:generate-entities ../../models/Entities

