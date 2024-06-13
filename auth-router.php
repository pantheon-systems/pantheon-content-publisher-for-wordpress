<?php

// Collect query parameters
$queryString = $_SERVER['QUERY_STRING'];

// Collect all headers
$headers = getallheaders();

// Build the target URL
$targetUrl = 'https://cleanwp.test/wp-admin/admin.php?page=pcc';
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
