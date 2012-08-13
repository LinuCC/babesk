{extends file=$loanParent}{block name=content}
{foreach $data as $datatmp}
<div>{$datatmp.title}, {$datatmp.publisher}
</div>
{/foreach}
{/block}