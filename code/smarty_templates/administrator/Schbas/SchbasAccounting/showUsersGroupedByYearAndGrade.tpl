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


<h2 class='module-header'>Sch&uuml;lerliste</h2>

{$modAction = "showUsersGroupedByYearAndGrade"}

<fieldset class="selectiveLink">
 <legend>Klasse</legend>
 {foreach $gradeAll as $grade}
  <a class="selectiveLink" href="index.php?section=Schbas|SchbasAccounting&action=1&gradeIdDesired={$grade.gradelevel}-{$grade.label}"
  {if $grade.ID == $gradeDesired}style="color:rgb(150,40,40);"{/if}
  >
  {$grade.gradelevel}{$grade.label}
  </a>
 {/foreach}
</fieldset>


<table class="table table-responsive table-striped table-hover">
 <thead>
  <tr>
   <th align='center'>ID</th>
   <th align='center'>Vorname</th>
   <th align='center'>Name</th>
   <th align='center'>Benutzername</th>
   <th align='center'>Klasse</th>
   <th align='center'>Zahlung f&uuml;r kommenden Jahrgang</th>
  </tr>
 </thead>
 <tbody>
  {foreach $users as $user}
  <tr>
   <td align="center">{$user.ID}</td>
   <td align="left">{$user.forename}</td>
   <td align="left">{$user.name}</td>
   <td align="left">{$user.username}</td>
   <td align="center">{$user.gradeLabel}</td>
   <td align="left">

   {if (isset($user.loanChoice) && $user.loanChoice=='nl')}
   		Selbstzahler
   {elseif (isset($user.loanChoice) && $user.loanChoice=='ls')}
   		Von Zahlung befreit
   {elseif (isset($user.loanChoice) && ($user.loanChoice=='ln' || $user.loanChoice=='lr'))}
   		<form onsubmit='return false;' >
   		<input type="text" id="Payment{$user.ID}" value='{$user.payedAmount}' size=5/> ( von {$user.amountToPay} &euro;,
   		{if ($user.loanChoice=='ln')}
   			normal
   		{else}
   			erm&auml;&szlig;igt
   		{/if}

   		)<br>
   		</form>
   {else}
   		Zettel wurde noch nicht gescannt
   {/if}


   </td>
  </tr>
  {/foreach}
 </tbody>
</table>


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
    alert('Zahlung erfasst!');
    location.reload();
  },
  error: function(data) {
   alert('Ein Fehler ist beim Senden der Zahlung aufgetreten!');
  }
 });
}

</script>


{/block}
