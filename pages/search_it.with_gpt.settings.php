<?php

$addon = rex_addon::get('search_it_with_gpt');

$form = rex_config_form::factory($addon->getName());

$field = $form->addInputField('text', 'token', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('search_it_with_gpt_config_token_label'));
$field->setNotice(rex_i18n::msg('search_it_with_gpt_config_token_notice'));

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $addon->i18n('search_it_with_gpt_config'), false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
