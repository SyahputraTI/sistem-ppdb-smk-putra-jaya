<?php

function excel_headers(): array
{
    return [
        'No', 'Nomor Pendaftaran', 'Tahun Ajaran', 'Nama', 'NISN', 'NIK', 'Tempat Lahir', 'Tanggal Lahir',
        'JK', 'Agama', 'Alamat', 'No. WA Siswa', 'Email', 'Asal Sekolah', 'Jurusan',
        'Nama Ayah', 'Pekerjaan Ayah', 'Nama Ibu', 'Pekerjaan Ibu', 'No. HP Ortu',
        'Status', 'Catatan Admin', 'Waktu Daftar',
    ];
}

function excel_row(int $no, array $r): array
{
    return [
        (string) $no,
        $r['nomor'],
        $r['tahun_ajaran'],
        $r['nama'],
        $r['nisn'],
        $r['nik'],
        $r['tempat_lahir'],
        $r['tanggal_lahir'],
        $r['jenis_kelamin'],
        $r['agama'],
        $r['alamat'],
        $r['no_hp'],
        $r['email'] ?? '',
        $r['asal_sekolah'],
        $r['jurusan'],
        $r['nama_ayah'],
        $r['pekerjaan_ayah'],
        $r['nama_ibu'],
        $r['pekerjaan_ibu'],
        $r['no_hp_ortu'],
        status_label($r['status']),
        $r['catatan_admin'] ?? '',
        $r['created_at'],
    ];
}

function sync_excel_export(): string
{
    $path = (string) config('excel_path');
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $rows = db()->query('SELECT * FROM registrations ORDER BY id ASC')->fetchAll();
    $data = [];
    foreach ($rows as $i => $r) {
        $data[] = excel_row($i + 1, $r);
    }
    write_xlsx($path, excel_headers(), $data, 'Rekap PPDB');
    return $path;
}

function write_xlsx(string $path, array $headers, array $rows, string $sheetName = 'Sheet1'): void
{
    $sheetName = preg_replace('/[\\/:?*\[\]]/', '', $sheetName) ?: 'Sheet1';
    $all = array_merge([$headers], $rows);
    $sheetXml = xlsx_sheet_xml($all);
    $files = [
        '[Content_Types].xml' => xlsx_content_types(),
        '_rels/.rels' => xlsx_rels(),
        'xl/workbook.xml' => xlsx_workbook($sheetName),
        'xl/_rels/workbook.xml.rels' => xlsx_workbook_rels(),
        'xl/styles.xml' => xlsx_styles(),
        'xl/worksheets/sheet1.xml' => $sheetXml,
    ];

    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Tidak bisa menulis file Excel.');
    }
    foreach ($files as $name => $xml) {
        $zip->addFromString($name, $xml);
    }
    $zip->close();
}

function xlsx_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function xlsx_cell_ref(int $col, int $row): string
{
    $name = '';
    $n = $col;
    while ($n > 0) {
        $n--;
        $name = chr(65 + ($n % 26)) . $name;
        $n = intdiv($n, 26);
    }
    return $name . $row;
}

function xlsx_sheet_xml(array $rows): string
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetData>';
    foreach ($rows as $rIndex => $row) {
        $rowNum = $rIndex + 1;
        $style = $rIndex === 0 ? ' s="1"' : '';
        $xml .= '<row r="' . $rowNum . '">';
        foreach (array_values($row) as $cIndex => $value) {
            $ref = xlsx_cell_ref($cIndex + 1, $rowNum);
            $text = xlsx_esc((string) $value);
            $xml .= '<c r="' . $ref . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $text . '</t></is></c>';
        }
        $xml .= '</row>';
    }
    return $xml . '</sheetData></worksheet>';
}

function xlsx_content_types(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';
}

function xlsx_rels(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';
}

function xlsx_workbook(string $name): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="' . xlsx_esc($name) . '" sheetId="1" r:id="rId1"/></sheets></workbook>';
}

function xlsx_workbook_rels(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';
}

function xlsx_styles(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
        . '<fills count="2"><fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill></fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf/></cellStyleXfs>'
        . '<cellXfs count="2"><xf/><xf fontId="1" applyFont="1"/></cellXfs>'
        . '</styleSheet>';
}
