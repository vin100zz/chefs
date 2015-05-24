<?php

class Logger
{  
	public static function log($payload)
	{
    $log = "[" . date('d/m/Y H:i:s') . "] $payload\n";
		file_put_contents("logs.txt", "$log", FILE_APPEND);
	}

}

?>