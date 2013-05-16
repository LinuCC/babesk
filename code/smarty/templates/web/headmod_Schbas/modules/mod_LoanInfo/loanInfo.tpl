{include file='web/header.tpl' title='Schulbuchausleihe'}

<h3>{$coverLetterTitle}</h3>
{$coverLetterText}<br/>

<h3>{$textOneTitle}</h3>
{$textOneText}<br/>
<h3>{$textTwoTitle}</h3>
{$textTwoText}<br/>
<h3>{$textThreeTitle}</h3>
{$textThreeText}<br/>

<h3>B&uuml;cherliste</h3><br/> 
{foreach $booklist as $book}
		{$book.subject} {$book.title} {$book.price} <br/>
{/foreach}

<h3>Bitte Zutreffendes ankreuzen und zur&uuml;ckgeben an das Sekretariat des Lessing-Gymnasiums bis zum TT.MM.JJJJ !</h3> 
<script type="text/javascript">
 $(function(){
 $("#loanForm").validate();
 });
</script>
<form action="index.php?section=Schbas|LoanInfo" method="post" id="loanForm">
    <fieldset>
      <label style="width:250px; float:left;">Vorname des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_vorname" class="required"/><br>
      <label style="width:250px;float:left;">Name des/der Erziehungsberechtigten:</label>
      <input type="text" name="eb_name" class="required"/><br>
      <label style="width:250px;float:left;">Anschrift:</label>
      <input type="text" name="eb_adress" class="required"/><br>
      <label style="width:250px;float:left;" class="required digits">Telefon:</label>
      <input type="text" name="eb_tel" /><br>
      Name, Vorname des Sch&uuml;lers/der Sch&uuml;lerin:<br/>
      Jahrgangsstufe {$gradeValue}<br/>
    </fieldset>
    
    <script type="text/javascript">
	
		
		 $(function () {
     var $divs = $('#ausleihe > div');
     $divs.hide();
     $('#div2').show();
     $('#radio1').on('change', function () {
        $divs.hide();
         $('#div1').show();
         $('input[id=loanNormal]').attr('checked', false);
         $('input[id=loanReduced]').attr('checked', false);
     });
      $('#radio2').on('change', function () {
         $divs.hide();
         $('#div2').show();
         $('input[id=loanNormal]').attr('checked', true);
         $('input[name=noLoanFee]').attr('checked',false);
         
     });
 });
	
</script>



An das<br/>
Lessing-Gymnasium<br/>

An der entgeltlichen Ausleihe von Lernmitteln im Schuljahr JJJJ/JJ<br/>
<input type="radio" name="loanChoice" value="noLoan" id="radio1" required /> nehmen wir nicht teil<br />
<input type="radio" name="loanChoice" value="loan" id="radio2" checked/> nehmen wir teil und melden uns hiermit verbindlich zu den in Ihrem Schreiben vom TT.MM.JJJJ genannten Bedingungen an.

<div id="ausleihe" >
    <div id="div1"><input type="checkbox" name="noLoanFee" value="true" /> <b>Wir geh&ouml;ren zu dem von der Zahlung des Entgelts befreiten Personenkreis. 
    Leistungsbescheid bzw. &auml;hnlicher Nachweis ist beigef&uuml;gt.</b> <br /></div>
    <div id="div2">
    	Den Betrag von<br>
    	<input type="radio" name="loanFee" value="loanNormal" id="loanNormal" checked required/> 56,00 Euro <br />
    	<input type="radio" name="loanFee" value="loanReduced" id="loanReduced" /> 45,00 Euro (bei mehr als zwei schulpflichtigen Kindern)<br />
    </div>
    
</div>
<input type="submit" value="Senden" />
</form>

{include file='web/footer.tpl'}
