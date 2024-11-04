<?php

namespace App\Services;

namespace App\Services;

use InvalidArgumentException;

class Doc2Txt
{
    private string $filename;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist: $filePath");
        }

        $this->filename = $filePath;
    }

    private function read_doc(): string
    {
        $fileHandle = fopen($this->filename, 'r');
        if ($fileHandle === false) {
            throw new InvalidArgumentException("Cannot open file: $this->filename");
        }

        $line = fread($fileHandle, filesize($this->filename));
        fclose($fileHandle);  // Close the file handle after reading

        $lines = explode(chr(0x0D), $line);
        $outtext = '';

        foreach ($lines as $thisline) {
            $pos = strpos($thisline, chr(0x00));
            if ($pos === false && strlen($thisline) > 0) {
                $outtext .= $thisline . ' ';
            }
        }

        return preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", '', $outtext);
    }

    private function read_docx(): string
    {
        $striped_content = '';
        $content = '';

        $zip = zip_open($this->filename);
        if (!$zip || is_numeric($zip)) {
            throw new InvalidArgumentException("Cannot open zip for file: $this->filename");
        }

        while ($zip_entry = zip_read($zip)) {
            if (!zip_entry_open($zip, $zip_entry)) {
                continue;
            }

            if (zip_entry_name($zip_entry) !== 'word/document.xml') {
                continue;
            }

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    public function convertToText(): string
    {
        if (!isset($this->filename) || !file_exists($this->filename)) {
            return "File does not exist.";
        }

        $fileArray = pathinfo($this->filename);
        $file_ext = strtolower($fileArray['extension']); // Convert extension to lower case for consistency

        if ($file_ext === 'doc') {
            return $this->read_doc();
        } elseif ($file_ext === 'docx') {
            return $this->read_docx();
        } else {
            return "Invalid file type.";
        }
    }
}
