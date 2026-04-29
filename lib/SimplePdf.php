<?php

// Lightweight PDF generator with basic table support (Helvetica).
// Note: This is not a full PDF library; it is tailored for the Users export.
class SimplePdf
{
    private $pages = [];

    public function addTablePage($title, $metaLines, $columns, $rows)
    {
        $this->pages[] = [
            'title' => (string)$title,
            'meta' => array_values(array_map('strval', (array)$metaLines)),
            'columns' => $columns,
            'rows' => $rows,
        ];
    }

    private function pdfEscape($text)
    {
        $text = (string)$text;
        // PDF "text strings" here assume WinAnsi/Windows-1252 for Helvetica.
        // Best-effort conversion to keep output valid even with accents.
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }
        // Fallback: replace non-ASCII bytes to avoid invalid PDF strings.
        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E\xA0-\xFF]/', '?', $text);
        $text = str_replace("\\", "\\\\", $text);
        $text = str_replace("(", "\\(", $text);
        $text = str_replace(")", "\\)", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace("\n", " ", $text);
        return $text;
    }

    private function opText($x, $y, $size, $text, $r = 0, $g = 0, $b = 0)
    {
        return implode("\n", [
            "BT",
            sprintf("/F1 %d Tf", (int)$size),
            sprintf("%.3f %.3f %.3f rg", $r, $g, $b),
            sprintf("1 0 0 1 %.2f %.2f Tm (%s) Tj", $x, $y, $this->pdfEscape($text)),
            "ET",
        ]);
    }

    private function opRectFillStroke($x, $y, $w, $h, $fillRgb, $strokeRgb, $lineWidth = 0.5)
    {
        return implode("\n", [
            sprintf("%.2f w", $lineWidth),
            sprintf("%.3f %.3f %.3f rg", $fillRgb[0], $fillRgb[1], $fillRgb[2]),
            sprintf("%.3f %.3f %.3f RG", $strokeRgb[0], $strokeRgb[1], $strokeRgb[2]),
            sprintf("%.2f %.2f %.2f %.2f re", $x, $y, $w, $h),
            "B",
        ]);
    }

    private function buildTableContentStream($pageTitle, $metaLines, $columns, $rows, $pageNo, $pageCount)
    {
        // A4 points: 595x842
        $pageW = 595;
        $pageH = 842;
        $marginX = 40;
        $marginTop = 40;
        $marginBottom = 40;

        $content = [];

        // Title
        $content[] = $this->opText($marginX, $pageH - $marginTop, 16, $pageTitle, 0.1, 0.12, 0.16);
        $y = $pageH - $marginTop - 22;

        // Meta lines
        foreach ($metaLines as $line) {
            $content[] = $this->opText($marginX, $y, 9, $line, 0.42, 0.45, 0.5);
            $y -= 12;
        }

        // Page indicator
        $content[] = $this->opText($pageW - $marginX - 120, $pageH - $marginTop, 9, "Page $pageNo/$pageCount", 0.42, 0.45, 0.5);

        $y -= 10;

        // Table geometry
        $tableX = $marginX;
        $tableW = $pageW - ($marginX * 2);
        $rowH = 18;
        $headerH = 20;

        // Columns: each item ['label'=>..., 'key'=>..., 'w'=>fraction]
        $colXs = [];
        $colWs = [];
        $x = $tableX;
        foreach ($columns as $c) {
            $w = (float)$c['w'];
            $cw = $tableW * $w;
            $colXs[] = $x;
            $colWs[] = $cw;
            $x += $cw;
        }

        // Header background
        $headerY = $y - $headerH;
        $content[] = $this->opRectFillStroke($tableX, $headerY, $tableW, $headerH, [0.12, 0.31, 0.85], [0.12, 0.31, 0.85], 0.8);

        // Header text + vertical separators
        for ($i = 0; $i < count($columns); $i++) {
            $label = (string)$columns[$i]['label'];
            $tx = $colXs[$i] + 6;
            $ty = $headerY + 6;
            $content[] = $this->opText($tx, $ty, 9, $label, 1, 1, 1);
        }

        // Row rendering
        $yCursor = $headerY;
        $stroke = [0.88, 0.9, 0.93];
        $alt1 = [1, 1, 1];
        $alt2 = [0.97, 0.98, 1.0];

        $rowIndex = 0;
        foreach ($rows as $r) {
            $rowTop = $yCursor;
            $rowBottom = $rowTop - $rowH;

            // background
            $fill = ($rowIndex % 2 === 0) ? $alt1 : $alt2;
            $content[] = $this->opRectFillStroke($tableX, $rowBottom, $tableW, $rowH, $fill, $stroke, 0.6);

            // cells
            for ($i = 0; $i < count($columns); $i++) {
                $key = (string)$columns[$i]['key'];
                $val = isset($r[$key]) ? (string)$r[$key] : '';

                // truncate to fit roughly
                $maxChars = (int)max(8, floor(($colWs[$i] - 10) / 5.2));
                if (strlen($val) > $maxChars) {
                    $val = substr($val, 0, max(0, $maxChars - 3)) . '...';
                }

                $tx = $colXs[$i] + 6;
                $ty = $rowBottom + 5;
                $content[] = $this->opText($tx, $ty, 9, $val, 0.12, 0.14, 0.18);
            }

            $yCursor = $rowBottom;
            $rowIndex++;
        }

        // Footer note
        $content[] = $this->opText($marginX, $marginBottom - 10, 8, "Export genere automatiquement", 0.55, 0.58, 0.62);

        return implode("\n", $content) . "\n";
    }

    public function output($filename = 'export.pdf')
    {
        $objects = [];
        $offsets = [];

        // 1: Catalog, 2: Pages, 3: Font
        $objects[] = "";
        $objects[] = "";
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        $pageObjIds = [];
        $nextId = 4;

        foreach ($this->pages as $p) {
            // paginate rows by available height
            $columns = (array)$p['columns'];
            $rows = (array)$p['rows'];

            $pageW = 595;
            $pageH = 842;
            $marginTop = 40;
            $marginBottom = 40;
            $rowH = 18;
            $headerH = 20;
            $titleBlock = 22 + (count($p['meta']) * 12) + 10; // title + meta + spacing

            $available = ($pageH - $marginTop - $marginBottom) - $titleBlock - $headerH - 30;
            $rowsPerPage = (int)max(5, floor($available / $rowH));

            $chunks = array_chunk($rows, $rowsPerPage);
            if (empty($chunks)) {
                $chunks = [[]];
            }
            $pageCount = count($chunks);
            $pageNo = 1;

            foreach ($chunks as $chunk) {
                $content = $this->buildTableContentStream($p['title'], $p['meta'], $columns, $chunk, $pageNo, $pageCount);
                $contentObjId = $nextId++;
                $pageObjId = $nextId++;

                $objects[$contentObjId - 1] =
                    "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";

                $objects[$pageObjId - 1] =
                    "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] " .
                    "/Resources << /Font << /F1 3 0 R >> >> " .
                    "/Contents " . $contentObjId . " 0 R >>";

                $pageObjIds[] = $pageObjId;
                $pageNo++;
            }
        }

        $kids = [];
        foreach ($pageObjIds as $id) {
            $kids[] = $id . " 0 R";
        }

        $objects[1 - 1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2 - 1] = "<< /Type /Pages /Kids [" . implode(" ", $kids) . "] /Count " . count($kids) . " >>";

        $pdf = "%PDF-1.4\n";
        $i = 1;
        foreach ($objects as $obj) {
            if ($obj === "") {
                $obj = "<<>>";
            }
            $offsets[$i] = strlen($pdf);
            $pdf .= $i . " 0 obj\n" . $obj . "\nendobj\n";
            $i++;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($j = 1; $j <= count($objects); $j++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$j]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit();
    }
}

 
