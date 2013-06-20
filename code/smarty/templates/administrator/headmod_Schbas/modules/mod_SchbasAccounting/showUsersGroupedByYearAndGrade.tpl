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

<script type="text/javascript">
function showOptions (ID) {
	document.getElementById('optionButtons' + ID).hidden = false;
	document.getElementById('option' + ID).hidden = true;
}
function sendPayment(ID){
		var ajax;
		
	try{
		ajax = new XMLHttpRequest();
	} catch (e){
		// IE
		try{
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				alert("Browser wird nicht unterstuetzt!");
				return false;
			}
		}
	}
	
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4){
			var barcodeField = document.getElementsByName('test')[0];
			barcodeField.value = ajax.responseText;
			
			
		}
	}
		
		payment = document.getElementsByName('payment' + ID)[0].value;
		queryString = "&payment=" + encodeURIComponent(payment) + "&ID="+ ID + "&ajax=1";
		alert("{$adress}" + queryString);
		ajax.open("GET", "{$adress}" + queryString, true);
		ajax.send();
}

function enter_pressed(e){
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return false;
return (keycode == 13);
}
</script>

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
			<input type="text" name="payment{$user.ID}" onKeyPress='if(enter_pressed(event)) sendPayment("{$user.ID}")'/><br>
			</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<form onsubmit='return false;' >
	<input type="text" name="test" /><br>
</form>


{/block}