<?php
$list = rex_list::factory('SELECT * FROM mf_googleplaces_reviews ORDER BY time DESC', 50);

$list->setColumnSortable('time');
$list->setColumnSortable('rating');

$list->setColumnLabel('author_name', $this->i18n('googleplaces_label_author_name'));
$list->setColumnLabel('profile_url', $this->i18n('googleplaces_label_profile_url'));
$list->setColumnLabel('profile_photo_url', $this->i18n('googleplaces_label_profile_photo_url'));
$list->setColumnLabel('rating', $this->i18n('googleplaces_label_rating'));
$list->setColumnLabel('text', $this->i18n('googleplaces_label_text'));
$list->setColumnLabel('time', $this->i18n('googleplaces_label_time'));

$list->setColumnFormat('time', 'date', 'd.m.Y - H:m');

$list->removeColumn('id');
$list->removeColumn('language');
$list->removeColumn('author_name');
$list->removeColumn('profile_photo_url');
$list->removeColumn('profile_photo_base64');
$list->removeColumn('author_url');
$list->removeColumn('createdate_addon');
$list->removeColumn('google_place_id');



$list->addColumn($this->i18n('googleplaces_label_author'), '', 2, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="text-center"><a href="###author_url###" target="_blank" class=""><img src="###profile_photo_url###" width="25"><br>###author_name###</a></td>']);

$content = $list->get();

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('googleplaces_reviews'), false); //translate
$fragment->setVar('content', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
