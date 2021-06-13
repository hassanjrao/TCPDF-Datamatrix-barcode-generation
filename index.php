<?php
require_once('vendor/autoload.php');

use setasign\Fpdi\Tcpdf\Fpdi;

// ini_set('memory_limit', '-1');

$data = htmlentities(preg_replace("/\r|\n|\s+/", "", file_get_contents("data.xml")));

// var_dump(mb_strlen($data,"UTF-8"));

function str_split_unicode($str, $l = 0)
{
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}



// var_dump($data);

// echo "<br><br><br>";


$data_char = str_split_unicode($data);

// var_dump($data_char);


$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
// /Merging of the existing PDF pages to the final PDF

$code = array();

$txt = "";

$h = 0;


// echo "<br><br><br>";

$total_char = count($data_char) - 1;

$d = $total_char / 1300;

$total_bars = floor($d);




// echo $total_bars;

for ($j = 0; $j < $total_bars; $j++) {

    $txt = "";

    for ($k = $h; $k < $total_char; $k++) {

        echo $k."-";
        $txt .= $data_char[$k];
        $h++;
        if (mb_strlen($txt,"UTF-8") == 1300) {

            echo "<br><br>";

            $total_char += 1300;
            break;
        }
    }



    $code[$j] = $txt;
}


if (is_float($d)) {
    $last_bar_start_with = $total_bars * 1300;
    $txt = "";

    for ($i = $last_bar_start_with; $i < count($data_char) - 1; $i++) {
        $txt .= $data_char[$i];
    }

    $code[$j ] = $txt;

    $total_bars+=1;
}
echo "<pre>";
var_dump($code);
echo "</pre>";


$pdf = new Fpdi();


$pageCount = $pdf->setSourceFile('template.pdf');
for ($i = 2; $i < $total_bars; $i++) {



    if ($i == 2) {

        $tplIdx = $pdf->importPage(1);
        $pdf->AddPage();

        $pdf->SetFont('freeserif', '', 12);
        $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[0]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 24, 71, 66, 66, $style, 'N');

        $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[1]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 24, 172, 66, 66, $style, 'N');

        $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[2]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 124, 172, 66, 66, $style, 'N');
    } else {


        $tplIdx = $pdf->importPage(2);
        $pdf->AddPage();

        $pdf->SetFont('freeserif', '', 12);
        if ($i < $total_bars) {
            $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[$i++]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 24, 71, 66, 66, $style, 'N');
        } else {
        }

        if ($i < $total_bars) {
            $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[$i++]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 124, 71, 66, 66, $style, 'N');
        }


        if ($i < $total_bars) {
            $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[$i++]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 24, 172, 66, 66, $style, 'N');
        }


        if ($i < $total_bars) {
            $pdf->write2DBarcode(html_entity_decode(htmlspecialchars_decode($code[$i]), ENT_XML1, "UTF-8"), 'DATAMATRIX', 124, 172, 66, 66, $style, 'N');
        }
    }

    // $pdf->write2DBarcode($code[3], 'DATAMATRIX', 17, 600, 80, 80, $style, 'N');




    $pdf->useTemplate($tplIdx);
}


// $pdf->Output('generated.pdf', 'I');
//Your code relative to the invoice here

// $pdf->Output();

// // $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
// $pdf->SetAutoPageBreak(true, 40);

// // add a page
// $pdf->AddPage();

// // get external file content
// $utf8text = file_get_contents('vendor/tecnickcom/tcpdf/examples/data/utf8test.txt', true);

// // $pdf->SetFont('freeserif', '', 12);
// // now write some text above the imported page
// $pdf->Write(5, $utf8text);

// $pdf->Output('generated.pdf', 'I');




// class Pdf extends Fpdi
// {
//     /**
//      * "Remembers" the template id of the imported page
//      */
//     protected $tplId;

//     /**
//      * Draw an imported PDF logo on every page
//      */
//     function Header()
//     {
//         if ($this->tplId === null) {
//             $this->setSourceFile('template.pdf');
//             $this->tplId = $this->importPage(1);
//         }
//         $size = $this->useImportedPage($this->tplId, 130, 5, 60);

//         $this->SetFont('freesans', 'B', 20);
//         $this->SetTextColor(0);
//         $this->SetXY(PDF_MARGIN_LEFT, 5);
//         $this->Cell(0, $size['height'], 'TCPDF and FPDI');
//     }

//     function Footer()
//     {
//         // emtpy method body
//     }
// }

// // initiate PDF
// $pdf = new Pdf();
// $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
// $pdf->SetAutoPageBreak(true, 40);

// // add a page
// $pdf->AddPage();

// // get external file content
// $utf8text = file_get_contents('vendor/tecnickcom/tcpdf/examples/data/utf8test.txt', true);

// $pdf->SetFont('freeserif', '', 12);
// // now write some text above the imported page
// $pdf->Write(5, $utf8text);
