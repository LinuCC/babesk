{extends file=$base_path} {block name=content}

{if $message && $message != ''}{$message}{/if}

{if $error && $error != ''}<p class="error">Ein Fehler ist aufgetreten:<br>{$error}</p>{/if}

{/block}
