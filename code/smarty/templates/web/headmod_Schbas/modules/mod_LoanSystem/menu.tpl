{include file='web/header.tpl' title='Schulbuchausleihe'}



			<center><h2>Schulbuchausleihsystem f&uuml;r das Schuljahr {$schbasYear}</h2></center>	
		<div align="center">
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<table cellspacing="20"><tr><td>
			<div id="order">
			<form action="index.php?section=Schbas|LoanSystem&action=showPdf" method="post" name="loanInfo" id="loanInfo">		
				<h3><a href="javascript:document.loanInfo.submit()">Informationen</a></h3>
			</form>
			</div></td><td>
			<div id="order">
			<form action="index.php?section=Schbas|LoanSystem" method="post" name="loanShowForm" id="loanShowForm">	
			<input type="hidden" name="loanShowForm">	
				<h3><a href="javascript:document.loanShowForm.submit()">Anmeldeformular</a></h3>
			</form>
			</div>
			</td>
			<td>
			<div id="order">
			<form action="index.php?section=Schbas|LoanSystem" method="post" name="loanShowBuy" id="loanShowBuy">	
			<input type="hidden" name="loanShowBuy">	
				<h3><a href="javascript:document.loanShowBuy.submit()">Selbstk&auml;ufe</a></h3>
			</form>
			</div>
			</td>
			</tr></table>
			{/if}
    </div>
{if $loanShowBuy}
<form action="index.php?section=Schbas|LoanSystem&action=loanShowBuy" method="post" id="loanShowBuy">
<input type="hidden" name="loanShowBuySave">
<h5>Folgende B&uuml;cher werden f&uuml;r das kommende Schuljahr ben&ouml;tigt. Sie k&ouml;nnen in der Liste angeklickt werden, wenn sie selbst angeschafft werden. Bei der Buchausgabe werden sie dann nicht ausgegeben.</h5>
{foreach from=$loanbooks item=book}
<input type="checkbox" name="bookID[]" value="{$book.id}" {if $book.selected}checked{/if}>{$book.subject}: {$book.title} ({$book.author}, {$book.publisher}. ISBN: {$book.isbn})<br>
{/foreach}
<input type="submit" value="Selbstk&auml;ufe abspeichern" />
</form>

{/if}
			
{if $loanShowForm}
			<h3>Anmeldeformular</h3>




<div class="schbasForm"  style="border-color: #df610c;">
<h5>Die Eingaben in diesem orangefarbenen Rahmen sind freiwillig. Sie werden direkt in das R&uuml;ckmeldedokument ausgegeben und <u>nicht</u> abgespeichert. 
Entweder geben Sie diese Daten hier online oder nach dem Ausdrucken des erstellten R&uuml;ckmeldedokuments handschriftlich ein.
<form action="index.php?section=Schbas|LoanSystem&action=showFormPdf" method="post" id="loanForm">
    <fieldset>
      <label style="width:300px; float:left;" for="eb_vorname">Vorname des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_vorname"/><br> 
      <label style="width:300px;float:left;"for="eb_name">Name des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_name"/><br>
      <label style="width:300px;float:left;" for="eb_adress">Anschrift:</label>
      <textarea name="eb_adress" rows=2 cols=20 style="resize: none;"></textarea><br>
      <label style="width:300px;float:left;" for="eb_tel">Telefon:</label>
      <input type="text" name="eb_tel" /><br></h5> 
    </fieldset>
</div>

    <script type="text/javascript">
	
		
		 $(function () {
     var $divs = $('#ausleihe > div');
     $('#div3').hide();
     $divs.hide();
     $('#div2').show();
     $('#radio1').on('change', function () {
     $('#div3').hide();
        $divs.hide();
         $('#div1').show();
         $('input[id=loanNormal]').prop('checked', false);
         $('input[id=loanReduced]').prop('checked', false);
         $('input[id=loanSoli]').prop('checked', false);
         $('textarea[name=siblings]').val('');
     });
      $('#radio2').on('change', function () {
      $('#div3').hide();
         $divs.hide();
         $('#div2').show();
         $('input[id=loanNormal]').prop('checked', true);
         $('textarea[name=siblings]').val('');
       
     });
     $('#loanReduced').on('change', function () {
         $('#div3').show();
     });
     $('#loanNormal').on('change', function () {
         $('#div3').hide();
          $('textarea[name=siblings]').val('');
     });
     $('#loanSoli').on('change', function () {
         $('#div3').hide();
       $('textarea[name=siblings]').val('');
     }); 
 });
	
</script>

<style type='text/css'  media='all'>




div.schbasForm {

	border-style: solid;
	border-width: 1px;
	border-color: #2e6132;
	-webkit-border-radius: 20px;
  -khtml-border-radius: 20px;
  -moz-border-radius: 20px;
	border-radius: 20px;
	margin: 0 auto;
	padding: 15px;
	width: 650px;
}



</style>

<div class="schbasForm">
<h5>In diesem gr&uuml;nen Bereich m&uuml;ssen Sie eine Auswahl treffen! Ihre Entscheidung erscheint im erstellten R&uuml;ckmeldedokument als Strichcode.<br/>
An der entgeltlichen Ausleihe von Lernmitteln im Schuljahr {$schbasYear}<br/>
<input type="radio" name="loanChoice" value="noLoan" id="radio1" required /> nehmen wir nicht teil<br />
<input type="radio" name="loanChoice" value="loan" id="radio2" checked/> nehmen wir teil und melden uns hiermit verbindlich zu den im oben abrufbaren Schreiben genannten Bedingungen an.

<div id="ausleihe" >
    <div id="div2">
    	Den Betrag von<br>
    	<input type="radio" name="loanFee" value="loanNormal" id="loanNormal" checked /> {$feeNormal} Euro <br />
    	<input type="radio" name="loanFee" value="loanReduced" id="loanReduced" /> {$feeReduced} Euro (bei mehr als zwei schulpflichtigen Kindern)<br />
    	<input type="radio" name="loanFee" value="loanSoli" id="loanSoli"/> Wir geh&ouml;ren zu dem von der Zahlung des Entgelts befreiten Personenkreis. 
   															   Leistungsbescheid bzw. &auml;hnlicher Nachweis ist beigef&uuml;gt. </h5>
    	<div id="div3" class="schbasForm"  style="border-color: #df610c; width:600px;">
    	<h5>Die Eingaben in diesem orangefarbenen Rahmen sind freiwillig. Sie werden direkt in das R&uuml;ckmeldedokument ausgegeben und nicht abgespeichert. 
    	Entweder geben Sie diese Daten hier online oder nach dem Ausdrucken des erstellten R&uuml;ckmeldedokuments handschriftlich ein.<br/>
    	Weitere schulpflichtige Kinder im Haushalt (Schuljahr {$schbasYear}).<br/> Bitte pro Zeile den Namen, Vornamen und die Schule angeben, auf der das jeweilige Kind geht.</h5>
    	<textarea name="siblings" rows=5 cols=80></textarea>
    	</div>
    </div>
    
</div>
<input type="submit" value="R&uuml;ckmeldedokument erstellen" />
</form>
	
	
	<script type="text/javascript">
$('#loanForm').submit(function() {

  var text = $("textarea[name=siblings]").val();
  var lines = text.split("\n");
  var linesLen = lines.length;
 
  
if ($("textarea[name=siblings]").val() && $('input[id=loanReduced]').prop('checked') && linesLen<2) {
  alert('Bitte geben Sie mindestens zwei Kinder (in zwei Zeilen) ein!');
  return false;
  }
});
</script>	
		
{/if}
{include file='web/footer.tpl'}