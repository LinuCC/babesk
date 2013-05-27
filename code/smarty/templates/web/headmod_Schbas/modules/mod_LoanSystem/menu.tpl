{include file='web/header.tpl' title='Schulbuchausleihe'}



			<center><h2>Schulbuchausleihsystem</h2><br/></center>	
			<h3>Anschreiben und Buchliste f&uuml;r das Schuljahr {$schbasYear}</h3>
			{if $BaBeSkTerminal}
				Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden!
			{else}
			<a href="index.php?section=Schbas|LoanSystem&action=showPdf">
				<input type="submit" value="Informationen herunterladen" /></a>
			{/if}
			
	<br/>	<br/>	
			<h3>Anmeldeformular</h3>


<script type="text/javascript">
 $(function(){
 $("#loanForm").validate();
 });
</script>

<div class="schbasForm"  style="border-color: #df610c;">
<h5>Die Eingaben in diesem orangefarbenen Rahmen sind freiwillig. Sie werden direkt in das R&uuml;ckmeldedokument ausgegeben und <u>nicht</u> abgespeichert. 
Entweder geben Sie diese Daten hier online oder nach dem Ausdrucken des erstellten R&uuml;ckmeldedokuments handschriftlich ein.
<form action="index.php?section=Schbas|LoanSystem&action=showFormPdf" method="post" id="loanForm">
    <fieldset>
      <label style="width:300px; float:left;">Vorname des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_vorname"/><br>
      <label style="width:300px;float:left;">Name des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_name"/><br>
      <label style="width:300px;float:left;">Anschrift:</label>
      <input type="text" name="eb_adress"/><br>
      <label style="width:300px;float:left;">Telefon:</label>
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
     });
      $('#radio2').on('change', function () {
      $('#div3').hide();
         $divs.hide();
         $('#div2').show();
         $('input[id=loanNormal]').prop('checked', true);
       
     });
     $('#loanReduced').on('change', function () {
         $('#div3').show();
     });
     $('#loanNormal').on('change', function () {
         $('#div3').hide();
     });
     $('#loanSoli').on('change', function () {
         $('#div3').hide();
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
    	<input type="radio" name="loanFee" value="loanNormal" id="loanNormal" checked required/> {$feeNormal} Euro <br />
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
		
		

{include file='web/footer.tpl'}