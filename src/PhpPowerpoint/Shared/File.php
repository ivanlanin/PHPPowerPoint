<?php
/**
 * This file is part of PHPPowerPoint - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPowerPoint is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPPowerPoint
 * @copyright   2009-2014 PHPPowerPoint contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpPowerpoint\Shared;

/**
 * PHPPowerPoint_Shared_File
 */
class File
{
    /**
     * Verify if a file exists
     *
     * @param  string $pFilename Filename
     * @return bool
     */
    public static function fileExists($pFilename)
    {
        // Sick construction, but it seems that
        // file_exists returns strange values when
        // doing the original file_exists on ZIP archives...
        if (strtolower(substr($pFilename, 0, 3)) == 'zip') {
            // Open ZIP file and verify if the file exists
            $zipFile     = substr($pFilename, 6, strpos($pFilename, '#') - 6);
            $archiveFile = substr($pFilename, strpos($pFilename, '#') + 1);

            $zip = new \ZipArchive();
            if ($zip->open($zipFile) === true) {
                $returnValue = ($zip->getFromName($archiveFile) !== false);
                $zip->close();

                return $returnValue;
            } else {
                return false;
            }
        } else {
            // Regular file_exists
            return file_exists($pFilename);
        }
    }

    /**
     * Returns canonicalized absolute pathname, also for ZIP archives
     *
     * @param  string $pFilename
     * @return string
     */
    public static function realpath($pFilename)
    {
        // Try using realpath()
        $returnValue = realpath($pFilename);

        // Found something?
        if ($returnValue == '' || is_null($returnValue)) {
            $pathArray = split('/', $pFilename);
            while (in_array('..', $pathArray) && $pathArray[0] != '..') {
                for ($i = 0; $i < count($pathArray); ++$i) {
                    if ($pathArray[$i] == '..' && $i > 0) {
                        unset($pathArray[$i]);
                        unset($pathArray[$i - 1]);
                        break;
                    }
                }
            }
            $returnValue = implode('/', $pathArray);
        }

        // Return
        return $returnValue;
    }
}
