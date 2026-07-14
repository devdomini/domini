<?php
$c = @file_get_contents('http://127.0.0.1:8078/admin/login');
echo 'len='.strlen($c)."\n";
echo 'token='.(strpos($c, '_token') !== false ? 'yes' : 'no')."\n";
echo 'expired='.(strpos($c, 'Page Expired') !== false ? 'yes' : 'no')."\n";
