{extends file=$inh_path} {block name=content append}
{nocache}
{if $message}{$message}{/if}
{if $error}<p class="error">Ein Fehler ist aufgetreten:<br>{$error}</p>{/if}
{/nocache}

{/block}