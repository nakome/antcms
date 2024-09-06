<?php

declare (strict_types = 1);

defined('ACCESS') or die('Acceso denegado');

return [
    // date
    '{Date}' => '<?php echo date("d-m-Y");?>',

    // next day
    '{NextDay}' => '<?php echo date("Y-m-d" ,strtotime(date("d-m-Y") . "  +1 day"));?>',

    // year
    '{Year}' => '<?php echo date("Y");?>',

    // site url
    '{Site_url}' => '<?php echo SITE_URL;?>',
    '{Site_current}' => '<?php echo Vendor\Url\Url::current();?>',

    // comment
    //{* comment *}
    '{\*(.*?)\*}' => '<?php echo "\n";?>',

    // confitional
    '{If: ([^}]*)}' => '<?php if ($1): ?>',
    '{Else}' => '<?php else: ?>',
    '{Elseif: ([^}]*)}' => '<?php elseif ($1): ?>',
    '{\/If}' => '<?php endif; ?>',

    // segments
    '{Segment: ([^}]*)}' => '<?php if (Vendor\Url\Url::current() == "$1"): ?>',
    '{\/Segment}' => '<?php endif; ?>',

    // loop
    '{Loop: ([^}]*) as ([^}]*)=>([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $2=>$3): ?>',
    '{Loop: ([^}]*) as ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $2): ?>',
    '{Loop: ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $value): ?>',
    '{\/Loop}' => '<?php $counter++; endforeach; ?>',

    // {?= 'hello world' ?}
    '{\?(\=){0,1}([^}]*)\?}' => '<?php if(strlen("$1")) echo $2; else $2; ?>',

    // {? 'hello world' ?}
    '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo %%$1; ?>',

    // capitalize
    '{(\$[a-zA-Z\áéíóúñÁÉÍÓÚÑ\-._\[\]\'"0-9]+)\|capitalize}' => '<?php echo ucfirst(%%$1); ?>',

    // lowercase
    '{(\$[a-zA-Z\áéíóúñÁÉÍÓÑ\-._\[\]\'"0-9]+)\|lower}' => '<?php echo mb_strtolower(%%$1, "UTF-8"); ?>',

    // uppercase
    '{(\$[a-zA-Z\áéíóúñÁÉÍÓÚÑ\-._\[\]\'"0-9]+)\|upper}' => '<?php echo mb_strtoupper(%%$1); ?>',

    // truncate
    '{(\$[a-zA-Z\áéíóúñÁÉÍÓÚÑ\-._\[\]\'"0-9]+)\|truncate:(\d+)}' => '<?php echo truncate(%%$1, $2); ?>',

    // format time 'hace 13 días'
    '{(\$[a-zA-Z\-._\[\]\'"0-9]+)\|format_time}' => '<?php echo formatTime($1); ?>',

    // actions
    '{Action: ([a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php Vendor\Action\Action::run(\'$1\'); ?>',

    // include
    '{Include: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/$1"); ?>',

    // blocks
    '{Block: ([a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo Vendor\File\File::get(ROOT."/public/blocks/$1.html"); ?>',

    // partial
    '{Partial: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/public/views/partials/$1"); ?>',

    // assets
    '{Assets: (.+?\.[a-z]{2,4})}' => '<?php echo Vendor\Url\Url::base()."/public/$1" ?>',

    // Style css
    '{Style}' => '<?php echo "<style rel=\"stylesheet\" type=\"text/css\" nonce=\"'.NONCE.'\">";?>',
    '{/Style}' => '<?php echo "</style>";?>',

    // Javascript
    '{Script}' => '<?php echo "<script rel=\"javascript\" nonce=\"'.NONCE.'\">";?>',
    '{/Script}' => '<?php echo "</script>";?>',

    // Details
    '{Details: ([a-zA-Z\áéíóúñ\s]+)}' => '<?php echo "<details><summary>$1</summary><div class=\"details-body border-top border-3 border-primary bg-white text-dark p-2 fw-normal\">"; ?>',
    '{/Details}' => '<?php echo "</div></details>"; ?>',

    // Code
    '{Code: ([a-zA-Z]+)}' => '<?php echo "<pre class=\"p-2 px-3 mb-3 language-$1\"><code class=\"language-$1\">" ?>',
    '{/Code}' => '<?php echo "</code></pre>" ?>',
];