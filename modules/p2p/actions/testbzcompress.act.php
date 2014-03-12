<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/bzutils.inc.php');

//-------------------------------------------------------------------------------------------------
//*	action to test bzip compression
//-------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$txt = "
       bzip2  compresses  large  files  in  blocks.   The block size affects both the compression ratio achieved, and the amount of memory
       needed for compression and decompression.  The flags -1 through -9 specify the block size to be 100,000 bytes through 900,000 bytes
       (the  default)  respectively.  At decompression time, the block size used for compression is read from the header of the compressed
       file, and bunzip2 then allocates itself just enough memory to decompress the file.  Since block  sizes  are  stored  in  compressed
       files, it follows that the flags -1 to -9 are irrelevant to and so ignored during decompression.

       Compression and decompression requirements, in bytes, can be estimated as:

              Compression:   400 k + ( 8 x block size )

              Decompression: 100 k + ( 4 x block size ), or
                             100 k + ( 2.5 x block size )

       Larger  block sizes give rapidly diminishing marginal returns.  Most of the compression comes from the first two or three hundred k
       of block size, a fact worth bearing in mind when using bzip2 on small machines.  It is also important to appreciate that the decom‐
       pression memory requirement is set at compression time by the choice of block size.

       For  files  compressed  with the default 900 k block size, bunzip2 will require about 3700 kbytes to decompress.  To support decom‐
       pression of any file on a 4 megabyte machine, bunzip2 has an option to decompress using approximately half this amount  of  memory,
       about  2300  kbytes.  Decompression speed is also halved, so you should use this option only where necessary.  The relevant flag is
       -s.

       In general, try and use the largest block size memory constraints allow, since that maximises the compression  achieved.   Compres‐
       sion and decompression speed are virtually unaffected by block size.

       Another significant point applies to files which fit in a single block -- that means most files you'd encounter using a large block
       size.  The amount of real memory touched is proportional to the size of the file, since the file is  smaller  than  a  block.   For
       example,  compressing  a file 20,000 bytes long with the flag -9 will cause the compressor to allocate around 7600 k of memory, but
       only touch 400 k + 20000 * 8 = 560 kbytes of it.  Similarly, the decompressor will allocate 3700 k but only touch 100 k + 20000 * 4
       = 180 kbytes.

       Here  is  a  table which summarises the maximum memory usage for different block sizes.  Also recorded is the total compressed size
       for 14 files of the Calgary Text Compression Corpus totalling 3,141,622 bytes.  This column gives some  feel  for  how  compression
       varies with block size.  These figures tend to understate the advantage of larger block sizes for larger files, since the Corpus is
       dominated by smaller files.
";

	$compressed = p2p_bzcompress($txt, true);

	$decompressed = p2p_bzdecompress($compressed, true);

	echo "<h2>Test text compressed and decompressed:</h2>\n";
	echo "<pre>$decompressed</pre>\n";

?>
