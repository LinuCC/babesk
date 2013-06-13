{extends file=$booklistParent}{block name=content}
Das Buch wurde verändert:<br><br>
ID: {$id}<br>
Fach: {$subject}<br>
Klasse: {$class}<br>
Titel: {$title}<br>
Autor: {$author}<br>
Verlag: {$publisher}<br>
ISBN: {$isbn}<br>
Preis: {$price}<br>
Bundle: {$bundle}<br>
{/block}