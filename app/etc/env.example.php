<?php
$env_data = getenv("env_data",true)? : "W10=";
return eval(base64_decode($env_data));