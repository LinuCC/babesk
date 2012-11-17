{extends file=$inventoryParent}{block name=content}
Das Inventar wurde hinzugef&uuml;gt:<br><br>
Buch-ID: {$book_info.id}<br>
Fach: {$book_info.subject}<br>
Jahrgang: {$book_info.class}<br>
Titel: {$book_info.title}<br>
Autor: {$book_info.author}<br>
Verlag: {$book_info.publisher}<br>
ISBN: {$book_info.isbn}<br>
Preis: {$book_info.price}<br>
Bundle: {$book_info.bundle}<br>
Kaufjahr: {$purchase}<br>
Exemplar: {$exemplar}<br>
{/block}