<?php

namespace App\Services\Star;

class LineJob
{
    const SLM_AUTO_LOGO_OFF = "1B1D2F3100";
    const SLM_AUTO_LOGO_ON = "1B1D2F3110";
    const SLM_AUTO_LOGO_ON_SIMPLE = "1B1D2F3120";
    const SLM_INITIALIZE_RASTER_MODE = "1B2A7252";
    const SLM_NEW_LINE_HEX = "0A";
    const SLM_SET_EMPHASIZED_HEX = "1B45";
    const SLM_CANCEL_EMPHASIZED_HEX = "1B46";
    const SLM_SET_LEFT_ALIGNMENT_HEX = "1B1D6100";
    const SLM_SET_CENTER_ALIGNMENT_HEX = "1B1D6101";
    const SLM_SET_RIGHT_ALIGNMENT_HEX = "1B1D6102";
    const SLM_FEED_FULL_CUT_HEX = "1B6402";
    const SLM_FEED_PARTIAL_CUT_HEX = "1B6403";
    const SLM_CODEPAGE_HEX = "1B1D74";
    const SLM_TOP_OF_FORM_HEX = "0C";
    const SLM_SET_FONT_HEX = "1B1E46";
    const SLM_EURO_HEX = "D5";
    const SLM_IBM437_SEPERATOR = "C4";

    protected $verbose = 1;
    protected $maxchars = 48;
    protected $printJobBuilder = "";

    /**
     * Define defaults like fonts and stuff
     * LineJob constructor.
     */
    public function __construct() {
        $this->setDefaultCodePage();
        $this->setDefaultFont();
    }

    public function setDefaultFont() {
        $this->setFont('00');
    }

    public function setDefaultCodePage() {
        $this->setCodepage("UTF-8");
    }

    /**
     * @param $string
     * @return string
     */
    protected function strToHex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++)  {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0'.$hexCode, -2);
        }
        return strToUpper($hex);
    }

    /**
     * @param $bbcode
     */
    public function fromBbCode($bbcode) {
        if($this->verbose) {
            \Illuminate\Support\Facades\Log::info('-- PROCESS BBCODE FOR PRINTING --');
        }

        $splitText = preg_split("/(\[[\/]{0,1}[a-z0-9]+[\/]{0,1}\])/", $bbcode,NULL,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if(is_array($splitText)) {
            foreach ($splitText as $textPart) {
                // Check if BB TAG
                if (
                    substr($textPart, 0, 1) == '['
                    && isset($this->mapBbCode()[$textPart])
                ) {
                    // Special case
                    if(in_array($textPart, ['[textline]', '[/textline]'])) {
                        if ($this->verbose) {
                            \Illuminate\Support\Facades\Log::info($textPart);
                        }

                        if($textPart == '[/textline]') {
                            $this->addTextLine('');
                        }
                    }
                    // Normal text
                    else {
                        $method = $this->mapBbCode()[$textPart];

                        if ($this->verbose) {
                            \Illuminate\Support\Facades\Log::info($method . '()');
                        }

                        if (!empty($method) && method_exists($this, $method)) {
                            $this->$method();

                            if ($this->verbose) {
                                \Illuminate\Support\Facades\Log::info('> Executed');
                            }
                        } elseif ($this->verbose) {
                            \Illuminate\Support\Facades\Log::info('> FAILED');
                        }
                    }
                } // Process text
                else {
                    $this->addText($textPart);

                    if ($this->verbose) {
                        \Illuminate\Support\Facades\Log::info($textPart);
                    }
                }
            }
        }
    }

    /**
     * @param $maxchars
     */
    public function setMaxChars($maxchars) {
        $this->maxchars = (int) $maxchars;
        return $this;
    }

    /**
     * @return string[]
     */
    protected function mapBbCode() {
        return [
            '[b]' => 'setTextEmphasized',
            '[/b]' => 'cancelTextEmphasized',
            '[center]' => 'setTextCenterAlign',
            '[/center]' => 'setTextLeftAlign',
            '[right]' => 'setTextRightAlign',
            '[/right]' => 'setTextLeftAlign',
            '[pagebreak/]' => 'addTopOfForm',
            '[br/]' => 'addNewLine',
            '[feedpartialcut/]' => 'setFeedPartialCut',
            '[feedfullcut/]' => 'setFeedFullCut',
            '[textline]' => 'addTextLine',
            '[/textline]' => '', // we do not need to do anything here
            '[space/]' => 'addSpace',
            '[tab/]' => 'addTab',
            '[h1]' => 'setH1',
            '[/h1]' => 'cancelH1',
            '[/euro]' => 'addEuro',
            '[/seperator]' => 'getSeperator',
        ];
    }

    // not working currently
    public function setAutoLogoOff() {
        $this->printJobBuilder .= self::SLM_AUTO_LOGO_OFF;
    }

    public function setAutoLogoOn() {
        $this->printJobBuilder .= self::SLM_AUTO_LOGO_ON;
    }

    public function setAutoLogoOnSimple() {
        $this->printJobBuilder .= self::SLM_AUTO_LOGO_ON_SIMPLE;
    }

    public function addTopOfForm() {
        $this->printJobBuilder .= self::SLM_TOP_OF_FORM_HEX;
    }

    public function initializeRasterMode() {
        $this->printJobBuilder .= self::SLM_INITIALIZE_RASTER_MODE;
    }

    public function setTextEmphasized() {
        $this->printJobBuilder .= self::SLM_SET_EMPHASIZED_HEX;
    }

    public function cancelTextEmphasized() {
        $this->printJobBuilder .= self::SLM_CANCEL_EMPHASIZED_HEX;
    }

    public function setTextLeftAlign() {
        $this->printJobBuilder .= self::SLM_SET_LEFT_ALIGNMENT_HEX;
    }

    public function setTextCenterAlign() {
        $this->printJobBuilder .= self::SLM_SET_CENTER_ALIGNMENT_HEX;
    }

    public function setTextRightAlign() {
        $this->printJobBuilder .= self::SLM_SET_RIGHT_ALIGNMENT_HEX;
    }

    public function setCodepage($codepage) {
        if($codepage == "UTF-8") {
            $this->printJobBuilder .= "1b1d295502003001"."1b1d295502004000";
        }
        elseif ($codepage == "1252") {
            $this->printJobBuilder .= self::SLM_CODEPAGE_HEX."20";
        }
        else {
            $this->printJobBuilder .= self::SLM_CODEPAGE_HEX.$codepage;
        }
    }

    public function addNvLogo($keycode) {
        $this->printJobBuilder .= "1B1C70".$keycode."00".self::SLM_NEW_LINE_HEX;
    }

    public function setH1() {
        $this->setFontMagnification(2, 2);
    }

    public function cancelH1() {
        $this->setFontMagnification(1,1);
    }

    public function setFontMagnification($width, $height) {
        $w = 0;
        $h = 0;

        if($width <= 1) {
            $w = 0;
        } elseif ($width >= 6) {
            $w = 5;
        } else {
            $w = $width - 1;
        }

        if($height <= 1) {
            $h = 0;
        } elseif ($height >= 6) {
            $h = 5;
        } else {
            $h = $height - 1;
        }

        $this->printJobBuilder .= "1B69"."0".$h."0".$w;
    }

    public function addHex($hex) {
        $this->printJobBuilder .= $hex;
    }

    public function addText($text) {
        $this->printJobBuilder .= $this->strToHex($text);
    }

    public function addTextLine($text) {
        $this->printJobBuilder .= $this->strToHex($text).self::SLM_NEW_LINE_HEX;
    }

    public function addNewLine($quantity = 1) {
        for ($i = 0; $i < $quantity; $i++)  {
            $this->printJobBuilder .= self::SLM_NEW_LINE_HEX;
        }
    }

    public function addSpace($quantity = 1) {
        for ($i = 0; $i < $quantity; $i++)  {
            $this->printJobBuilder .= $this->strToHex(' ');
        }
    }

    public function addTab($quantity = 1) {
        for ($i = 0; $i < $quantity; $i++)  {
            $this->printJobBuilder .= $this->strToHex("\t");
        }
    }

    public function addEuro() {
        $this->setCodepage('04'); // Codepage 858 (Multilingual)
        $this->printJobBuilder .= self::SLM_EURO_HEX;
        $this->setDefaultCodePage();
    }

    public function setFeedFullCut() {
        $this->printJobBuilder .= self::SLM_FEED_FULL_CUT_HEX;
    }

    public function setFeedPartialCut() {
        $this->printJobBuilder .= self::SLM_FEED_PARTIAL_CUT_HEX;
    }

    public function setFont($font) {
        if(!in_array($font, ['00', '01', '16'])) {
            $font = '00';
        }

        $this->printJobBuilder .= self::SLM_SET_FONT_HEX . $font;
    }

    public function getSeperator() {
        $this->setCodepage('01');
        $this->printJobBuilder .= str_repeat(self::SLM_IBM437_SEPERATOR, $this->maxchars);
        $this->setDefaultCodePage();
    }

    public function getColumnSeparatedData($columns) {
        $total_columns = count($columns);

        if ($total_columns == 0) return "";
        if ($total_columns == 1) return $columns[0];
        if ($total_columns == 2)
        {
            $total_characters = strlen($columns[0])+strlen($columns[1]);
            $total_whitespace = $this->maxchars - $total_characters;
            if ($total_whitespace < 0) return "";
            return $columns[0].str_repeat(" ", $total_whitespace).$columns[1];
        }

        $total_characters = 0;
        foreach ($columns as $column)
        {
            $total_characters += strlen($column);
        }
        $total_whitespace = $this->maxchars - $total_characters;
        if ($total_whitespace < 0) return "";
        $total_spaces = $total_columns-1;
        $space_width = floor($total_whitespace / $total_spaces);
        $result = $columns[0].str_repeat(" ", $space_width);
        for ($i = 1; $i < ($total_columns-1); $i++)
        {
            $result .= $columns[$i].str_repeat(" ", $space_width);
        }
        $result .= $columns[$total_columns-1];

        return $result;
    }

    public function getPrintJobData() {
        return hex2bin($this->printJobBuilder);
    }
}