<?php

namespace App\Http\FPDF;

use App\Models\Alunos;

class ROD_PDF extends PDF
{
    protected $aluno = null;
    protected $horizontal = 0;

    function setAluno(Alunos $aluno){
        $this->aluno = $aluno;
    }

    function Header(){

        global $a;
        global $numero;
        
        $this->SetFont('Times', '', 10);

        if($numero <> $this->aluno->numero){
            $a = 0;
        }

        if ($a == 1){
            $this->ln(2);
        }else{//Primeira Página
            $this->ln(32);
            $a = 1;
            $numero = $this->aluno->numero;
        }
        
        $this->Rect($this->getX(), ($this->getY() - 1), 278, 25);
        $this->Cell(0, 8, utf8_decode('DADOS PESSOAIS DO DISCENTE'), 0, 1, 'L');
        $this->Cell(148, 7, utf8_decode('Nome: ' . $this->aluno->nome_completo), 'B', 0, 'L');
        $this->SetX(($this->getX() + 3));
        $this->Cell(48, 7, utf8_decode('Número: ' . $this->aluno->numero), 'B', 0, 'L');
        $this->SetX(($this->getX() + 3));
        $this->Cell(75, 7, utf8_decode('Curso: '), 'B', 1, 'L');
        $this->Cell(78, 7, utf8_decode('Pel/Turma: ' . $this->aluno->turma->turma), 'B', 1, 'L');
        
        $this->horizontal = $this->getY();
    }

    function Footer(){
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
    }

    function getHorizontal(){
        return $this->horizontal;
    }

    /*
    MultiCell Table
    */
    var $widths;
    var $aligns;

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

    function Row($data, $height=5)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
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
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
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
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
    /*
    Fim MultiCell Table
    */
}
