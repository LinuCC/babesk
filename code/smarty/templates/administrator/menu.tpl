<h2 id="menu_header">Hauptmen&uuml;</h2>



<div id="container">
<ul id="tabnav">
  <a href="#first" id="tab_first" class="tab">BaBeSK</a> | 
  <a href="#second" id="tab_second" class="tab">SchuBu</a> | 
  <a href="#third" id="tab_third" class="tab">KuWa</a>
</ul>
<div id="panel_first" class="panel">
  {section name=module loop=$modules}
<div class="menu_item"><h4><a href="index.php?section={$modules[module]}&{$sid}">{$module_names[$modules[module]]}</a></h4></div>
{/section}
</div>
<div id="panel_second" class="panel">
  Here is the content for the second tab.
</div>
<div id="panel_third" class="panel">
  Here is the content for the third tab.
</div>     
</div>


<div id="close"></div>