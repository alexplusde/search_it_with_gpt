<?php

$addon = rex_addon::get('search_it_with_gpt');

/* Bei Installation einen API-Key speichern */
rex_config::set($addon->getName(), 'token', bin2hex(random_bytes(32)));
