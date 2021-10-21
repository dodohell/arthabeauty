<?
	include('config.inc.php');
	
	$file = $install_path.'/files/157393154940879.png';
	
	function compress_png($path_to_png_file, $max_quality = 90)
	{
		if (!file_exists($path_to_png_file)) {
			throw new Exception("File does not exist: $path_to_png_file");
		}

		// guarantee that quality won't be worse than that.
		$min_quality = 60;

		// '-' makes it use stdout, required to save to $compressed_png_content variable
		// '<' makes it read from the given file path
		// escapeshellarg() makes this safe to use with any path
		/*
		$compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

		if (!$compressed_png_content) {
			throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
		}
		*/
		$compressed_png_content = file_get_contents($path_to_png_file);


		return $compressed_png_content;
	}

	$read_from_path = $install_path.'files/157393154940879.png';
	$save_to_path = $install_path.'files/157393154940879.webp';
	/*
	$compressed_png_content = compress_png($read_from_path);
	file_put_contents($save_to_path, $compressed_png_content);
*/
	// you don't need move_uploaded_file().

	// and for webp:
	imagewebp(imagecreatefrompng($read_from_path)/*, $save_to_path + ".webp"*/);
?>
