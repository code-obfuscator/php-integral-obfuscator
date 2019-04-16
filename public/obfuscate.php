<?php

if (
	isset(
		$_FILES["file"]["tmp_name"],
		$_POST["key"],
		$_POST["shebang"]
	) &&
	is_string($_FILES["file"]["tmp_name"]) &&
	is_string($_POST["key"]) &&
	is_string($_POST["shebang"])
) {
	header("Content-Type: text/plain");

	if ($_POST["key"] === "") {
		printf("Key is empty, fallback to default: abc123\n");
		$_POST["key"];
	}

	$hash = sha1_file($_FILES["file"]["tmp_name"]);
	$inputFile = escapeshellarg(realpath(__DIR__."/../storage/raw/")."/{$hash}.tmp");
	$commands = [
		"mv -vf ".escapeshellarg($_FILES["file"]["tmp_name"])." {$inputFile}",
"../integralobf \\
	-o ".escapeshellarg($outputFile = realpath(__DIR__."/../storage/obfuscated")."/{$hash}.phx")." \\
	-k ".escapeshellarg($_POST["key"])." \\
	{$inputFile}"
	];
	foreach ($commands as $k => $cmd) {
		printf("- %s\n", $cmd);
		print shell_exec($cmd." 2>&1")."\n";
	}

	if (file_exists($outputFile)) {
		printf("Obfuscation success!\n");
		printf("Download obfuscated file: %s\n",
			"http".(isset($_SERVER["HTTPS"])?"s":"")."://".$_SERVER["HTTP_HOST"]."/obfuscated/{$hash}.phx"
		);
	}
}