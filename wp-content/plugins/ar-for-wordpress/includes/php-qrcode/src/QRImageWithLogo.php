<?php
/**
 * Class QRImageWithLogo
 *
 * @filesource   QRImageWithLogo.php
 * @created      18.11.2020
 * @package      chillerlan\QRCodeExamples
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2020 smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\QRCode;

use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\Output\QRImage;

class QRImageWithLogo extends QRImage{

	/**
	 * @param string|null $file
	 * @param string|null $logo
	 *
	 * @return string
	 * @throws \chillerlan\QRCode\Output\QRCodeOutputException
	 */
	public function dump(string $file = null, $logo = null): string {
		// Set returnResource to true to skip further processing for now
		$this->options->returnResource = true;

		try {
			// If the logo is a string, treat it as a file path and load the image

			if ( $logo instanceof \GdImage|| is_resource( $logo ) && 'gd' === get_resource_type( $logo )) {
				
				/*if (is_string($logo)) {
					//$logo = imagecreatefrompng($logo);
					$logo = imagecreatefromstring(file_get_contents($logo));
					
					// Check if the image creation was successful
					if (!$logo) {
						throw new QRCodeOutputException('Failed to load logo image');
					}
				} elseif (!is_resource($logo)) {
					throw new QRCodeOutputException('Invalid logo type');
				}*/

				$this->matrix->setLogoSpace(
					$this->options->logoSpaceWidth,
					$this->options->logoSpaceHeight
					// Not utilizing the position here
				);

				// There's no need to save the result of dump() into $this->image here
				parent::dump($file);

				// If a GD image resource was provided, use it directly
				
				$im = $logo;			
				
				// Get logo image size
				$w = imagesx($im);
				$h = imagesy($im);

				// Set new logo size, leave a border of 1 module (no proportional resize/centering)
				$lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
				$lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;

				// Get the QR code size
				$ql = $this->matrix->size() * $this->options->scale;

				// Scale the logo and copy it over. Done!
				imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);

				// Free up memory by destroying the logo image resource
				imagedestroy($im);

				$imageData = $this->dumpImage();

				if ($file !== null) {
					$this->saveToFile($imageData, $file);
				}

				if ($this->options->imageBase64) {
					$imageData = 'data:image/' . $this->options->outputType . ';base64,' . base64_encode($imageData);
				}

				return $imageData;

			}else{
				throw new QRCodeOutputException('Invalid logo type');
			}
		} catch (QRCodeOutputException $e) {
			// Handle the exception gracefully
			return 'Error: ' . $e->getMessage();
		}
	}
}
