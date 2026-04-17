<?php

namespace App\Http\FPDF;

class PDF_AUD_FO extends PDF
{
    var $B = 0;
    var $I = 0;
    var $U = 0;
    var $HREF = '';
    var $ALIGN = '';

    protected $dataInicial = null;
    protected $dataFinal = 0;
    protected $curso = null;

    function setPeriodos(string $dataInicial, string $dataFinal)
    {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
    }

    function Header()
    {

        $this->SetFont('Times', 'B', 8);

        $this->Image(public_path() . '/images/brasao-rfb.jpg', 95, 5, 20, 20);

        $this->SetXY(10, 26);
        $this->Cell(0, 3, utf8_decode('MINISTÉRIO DA DEFESA'), 0, 1, 'C', false);
        $this->Cell(0, 3, utf8_decode('EXÉRCITO BRASILEIRO'), 0, 1, 'C', false);
        $this->Cell(0, 3, 'ESCOLA DE SARGENTOS DAS ARMAS', 0, 1, 'C', false);
        $this->Cell(0, 3, '(ESCOLA SARGENTO MAX WOLFF FILHO)', 0, 1, 'C', false);

        $this->SetFont('Times', 'B', 10);
        $this->ln(5);
        $this->Cell(0, 4, utf8_decode('AUDIÊNCIA DE FATOS OBSERVADOS POR CURSO'), 0, 1, 'C', false);
        $this->SetFont('Times', 'B', 8);
        $this->Cell(0, 4, utf8_decode('Período: ' . $this->dataInicial . ' á ' . $this->dataFinal), 0, 1, 'C', false);
        $this->ln(5);


        $this->SetFillColor(230, 230, 230);
        $this->Cell(14, 6, utf8_decode('Data'), 0, 0, 'C', true);
        $this->Cell(15, 6, utf8_decode('Tipo de FO'), 0, 0, 'C', true);
        $this->Cell(30, 6, utf8_decode('Aluno'), 0, 0, 'C', true);
        $this->Cell(30, 6, utf8_decode('Observador'), 0, 0, 'C', true);
        $this->Cell(40, 6, utf8_decode('Fato Observado'), 0, 0, 'C', true);
        $this->Cell(40, 6, utf8_decode('Enquadramento NAPD'), 0, 0, 'C', true);
        $this->Cell(20, 6, utf8_decode('Ciente do Aluno'), 0, 1, 'C', true);
    }

    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-10);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    function WriteHTML($html)
    {
        //HTML parser
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                if ($this->HREF) {
                    $this->PutLink($this->HREF, $e);
                } elseif ($this->ALIGN == 'center') {
                    $this->Cell(0, 5, $e, 0, 1, 'C');
                } else {
                    $this->Write(5, $e);
                }
            } else {
                //Tag
                if ($e[0] == '/') {
                    $this->CloseTag(strtoupper(substr($e, 1)));
                } else {
                    //Extract properties
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $prop = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3)) {
                            $prop[strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->OpenTag($tag, $prop);
                }
            }
        }
    }

    function OpenTag($tag, $prop)
    {
        //Opening tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U') {
            $this->SetStyle($tag, true);
        }
        if ($tag == 'A') {
            $this->HREF = $prop['HREF'];
        }
        if ($tag == 'BR') {
            $this->Ln(5);
        }
        if ($tag == 'P') {
            $this->ALIGN = $prop['ALIGN'];
        }
        if ($tag == 'HR') {
            if (!empty($prop['WIDTH'])) {
                $Width = $prop['WIDTH'];
            } else {
                $Width = $this->w - $this->lMargin - $this->rMargin;
            }
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x, $y, $x + $Width, $y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U') {
            $this->SetStyle($tag, false);
        }
        if ($tag == 'A') {
            $this->HREF = '';
        }
        if ($tag == 'P') {
            $this->ALIGN = '';
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0) {
                $style .= $s;
            }
        }
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    /*
    MultiCell Table
    */
    var $widths;
    var $aligns;
    var $fontColor;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data, $height = 5)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = $height * $nb;

        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, $height, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
    /*
    Fim MultiCell Table
    */
}
