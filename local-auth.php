<?php

// Collect query parameters
$queryString = $_SERVER['QUERY_STRING'];

// Collect all headers
$headers = getallheaders();

// Build the target URL
$targetUrl = getenv('ROUTER_URL');
if (!$targetUrl) {
	echo 'The ROUTER_URL environment variable is not set.';
	exit();
}
if (!empty($queryString)) {
	$targetUrl .= '&' . $queryString;
}

// Perform the redirection
header("Location: $targetUrl", true, 302);

// Set the headers
foreach ($headers as $header => $value) {
	header("$header: $value");
}

exit();
