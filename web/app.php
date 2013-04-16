<?php
if (FALSE !== strpos($_SERVER['HTTP_HOST'], 'dev')) {
    include(__DIR__ . '/app_dev.php');
} else {
    include(__DIR__ . '/app_prod.php');
}