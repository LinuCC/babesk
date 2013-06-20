{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/
#main {
 width:1100px;
}

fieldset.selectiveLink {
 margin-left: 5%;
 margin-right: 5%;
 margin-bottom: 30px;
 border: 2px dashed rgb(100,100,100);
}

a.selectiveLink {
 padding: 5px;
}

.dataTable {
 margin: 0 auto;
}
</style>


<h2 class='moduleHeader'>Die Benutzer</h2>

{$modAction = "showUsersGroupedByYearAndGrade"}

<fieldset class="selectiveLink">
 <legend>Klasse</legend>
 {foreach $gradeAll as $grade}
  <a class="selectiveLink" href="index.php?section=Schbas|SchbasAccounting&action=1&gradeIdDesired={$grade.gradeValue}-{$grade.label}"
  {if $grade.ID == $gradeDesired}style="color:rgb(150,40,40);"{/if}
  >
  {$grade.gradeValue}{$grade.label}
  </a>
 {/foreach}
</fieldset>


<table class="dataTable">
 <thead>
  <tr>
   <th align='center'>ID</th>
   <th align='center'>Vorname</th>
   <th align='center'>Name</th>
   <th align='center'>Benutzername</th>
   <th align='center'>Klasse</th>
   <th align='center'>Zahlung</th>
  </tr>
 </thead>
 <tbody>
  {foreach $users as $user}
  <tr>
   <td align="center">{$user.ID}</td>
   <td align="center">{$user.forename}</td>
   <td align="center">{$user.name}</td>
   <td align="center">{$user.username}</td>
   <td align="center">{$user.gradeLabel}</td>
   <td align="center">
   <form onsubmit='return false;' >
   <input type="text" id="Payment{$user.ID}")'/><br>
   </form>
   </td>
  </tr>
  {/foreach}
 </tbody>
</table>
<form onsubmit='return false;' >
 <input type="text" name="test" /><br>
</form>


<script type="text/javascript" language="JavaScript">


 $('input[id^="Payment"]').on('keyup', function(event) {

 if(event.keyCode == 13) {
  sendUserReturnedBarcode($(this).val(),$(this).attr('id'));
 }
 });
 
 
 function sendUserReturnedBarcode(payment,uid) {
 $.ajax({
  'type': 'POST',
  'url': 'index.php?section=Schbas|SchbasAccounting&action=1&ajax=1',
  data: {
   'payment': payment,
   'ID'  : uid
  },
  success: function(data) {
  alert(data);
   if(data == 'error') {
    alert('Der Barcode ist nicht vollständig');
   }
   else if(data == 'entryNotFound') {
    alert('Der Link zwischen Nachricht und Benutzer konnte nicht gefunden werden');
   }
   else if(data == 'notValid') {
    alert('Der Barcode enthält inkorrekte Zeichen');
   }
   else if(data == 'dupe') {
    alert(unescape('Formular wurde bereits eingescannt. Bei %C4nderungen bitte zuerst l%F6schen!'));
   }
   else {
    alert('Einscannen erfolgreich!');
    location.reload();
   }
  },
  error: function(data) {
   alert('Ein Fehler ist beim Senden des Barcodes aufgetreten!');
  }
 });
}
 
</script>


{/block}