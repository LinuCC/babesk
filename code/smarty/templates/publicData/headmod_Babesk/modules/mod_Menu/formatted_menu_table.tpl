{extends file=$base_path} {block name=content}
{eval var=$menu_table assign="formatted_menu_table"}
{$formatted_menu_table}
{/block}