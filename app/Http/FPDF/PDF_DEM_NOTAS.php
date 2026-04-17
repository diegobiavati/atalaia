<?php

namespace App\Http\FPDF;

class PDF_DEM_NOTAS extends PDF
{
    var $script = '';

    function Header()
    {
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

    function CheckBox($pdf, $checked = true, $checkbox_size = 4, $ori_font_family = 'Arial', $ori_font_size = '10', $ori_font_style = '')
    {
        if ($checked == true) {
            $check = "4";
        } else {
            $check = "";
        }

        $pdf->SetFont('ZapfDingbats', '', $ori_font_size);
        $pdf->Cell($checkbox_size, $checkbox_size, $check, 1, 0);
        $pdf->SetFont($ori_font_family, $ori_font_style, $ori_font_size);
    }
}
