<?php

declare (strict_types = 1);

/*
 * Acceso restringido
 */
defined('SECURE') or die('No tiene acceso al script.');

return [
    // date
    '{Date}' => '<?php echo date("d-m-Y");?>',
    '{NextDay}' => '<?php echo date("Y-m-d" ,strtotime(date("d-m-Y") . "  +1 day"));?>',
    // year
    '{Year}' => '<?php echo date("Y");?>',
    // site url
    '{Site_url}' => '<?php echo AntCms\AntCMS::urlBase();?>',
    '{Site_current}' => '<?php echo AntCms\AntCMS::UrlCurrent();?>',
    // pagination for other folder of content not blog
    '{Pages: ([^}]*)}' => '<?php echo AntCms\AntCMS::actionRun("articles",["$1",AntCms\AntCMS::$config["pagination"]]);?>',
    // comment
    //{* comment *}
    '{\*(.*?)\*}' => '<?php echo "\n";?>',
    // confitional
    '{If: ([^}]*)}' => '<?php if ($1): ?>',
    '{Else}' => '<?php else: ?>',
    '{Elseif: ([^}]*)}' => '<?php elseif ($1): ?>',
    '{\/If}' => '<?php endif; ?>',
    // segments
    '{Segment: ([^}]*)}' => '<?php if (AntCms\AntCMS::urlCurrent() == "$1"): ?>',
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
    '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|capitalize}' => '<?php echo ucfirst(%%$1); ?>',
    // lowercase
    '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|lower}' => '<?php echo strtolower(%%$1); ?>',
    // actions
    '{Action: ([a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php AntCms\AntCMS::actionRun(\'$1\'); ?>',
    // include
    '{Include: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/$1"); ?>',
    // blocks
    '{Block: ([a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo AntCms\AntCMS::getFile(ROOT."/public/blocks/$1.html"); ?>',
    // partial
    '{Partial: (.+?\.[a-z]{2,4})}' => '<?php include_once(ROOT."/public/views/partials/$1"); ?>',
    // assets
    '{Assets: (.+?\.[a-z]{2,4})}' => '<?php echo AntCms\AntCMS::urlBase()."/public/$1" ?>',
    // Style css
    '{Style}' => '<?php echo "<style rel=\"stylesheet\" type=\"text/css\">";?>',
    '{/Style}' => '<?php echo "</style>";?>',
    // Javascript 
    '{Script}' => '<?php echo "<script rel=\"javascript\" type=\"module\">";?>',
    '{/Script}' => '<?php echo "</script>";?>',

    // Details
    '{Details: ([a-zA-Z\áéíóúñ\s]+)}' => '<?php echo "<details><summary>$1</summary><p>"; ?>',
    '{/Details}' => '<?php echo "</p></details>"; ?>',
    
    // Code
    '{Code: ([a-zA-Z]+)}' => '<?php echo "<pre><code class=\"language-$1\">" ?>',
    '{/Code}' => '<?php echo "</code></pre>" ?>',
];
