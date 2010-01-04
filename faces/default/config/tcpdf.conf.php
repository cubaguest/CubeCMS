<?php
/*
 * Konfigurace tcpdf
 * příklad lze nalézt v adresáři s knihovnou /lib/nonvve/tcpdf/
*/

define("K_TCPDF_EXTERNAL_CONFIG", true);

/**
 * page format
 */
define ('PDF_PAGE_FORMAT', 'A4');

/**
 * page orientation (P=portrait, L=landscape)
 */
define ('PDF_PAGE_ORIENTATION', 'P');

/**
 * document creator
 */
define ('PDF_CREATOR', 'TCPDF');

/**
 * document author
 */
define ('PDF_AUTHOR', 'TCPDF');

/**
 * header title
 */
define ('PDF_HEADER_TITLE', VVE_WEB_NAME);

/**
 * header description string
 */
define ('PDF_HEADER_STRING', (string)new Url_Link());

/**
 * image logo
 */
define ('PDF_HEADER_LOGO', '');

/**
 * header logo image width [mm]
 */
define ('PDF_HEADER_LOGO_WIDTH', 30);

/**
 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
 */
define ('PDF_UNIT', 'mm');

/**
 * header margin
 */
define ('PDF_MARGIN_HEADER', 5);

/**
 * footer margin
 */
define ('PDF_MARGIN_FOOTER', 10);

/**
 * top margin
 */
define ('PDF_MARGIN_TOP', 20);

/**
 * bottom margin
 */
define ('PDF_MARGIN_BOTTOM', 25);

/**
 * left margin
 */
define ('PDF_MARGIN_LEFT', 15);

/**
 * right margin
 */
define ('PDF_MARGIN_RIGHT', 15);

/**
 * default main font name
 */
define ('PDF_FONT_NAME_MAIN', 'freeserif');

/**
 * default main font size
 */
define ('PDF_FONT_SIZE_MAIN', 10);

/**
 * default data font name
 */
define ('PDF_FONT_NAME_DATA', 'freeserif');

/**
 * default data font size
 */
define ('PDF_FONT_SIZE_DATA', 8);

/**
 * default monospaced font name
 */
define ('PDF_FONT_MONOSPACED', 'courier');

/**
 * ratio used to adjust the conversion of pixels to user units
 */
define ('PDF_IMAGE_SCALE_RATIO', 1);

/**
 * magnification factor for titles
 */
define('HEAD_MAGNIFICATION', 1.1);

/**
 * height of cell repect font height
 */
define('K_CELL_HEIGHT_RATIO', 1.25);

/**
 * title magnification respect main font size
 */
define('K_TITLE_MAGNIFICATION', 1.3);

/**
 * reduction factor for small font
 */
define('K_SMALL_RATIO', 2/3);


?>
