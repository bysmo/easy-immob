<?php

namespace Tests\Unit;

use App\Domain\Lease\Services\DocxImporter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class DocxImporterTest extends TestCase
{
    public function test_it_converts_docx_xml_to_clean_html_without_regex_errors(): void
    {
        $importer = new DocxImporter();

        $reflection = new \ReflectionClass(DocxImporter::class);
        $method = $reflection->getMethod('parseXmlToHtml');
        $method->setAccessible(true);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p>
                    <w:pPr><w:pStyle w:val="Heading1"/></w:pPr>
                    <w:r><w:t>CONTRAT DE BAIL HABITATION</w:t></w:r>
                </w:p>
                <w:p>
                    <w:r>
                        <w:rPr><w:b/></w:rPr>
                        <w:t>Bailleur : {proprietaire_nom_complet}</w:t>
                    </w:r>
                </w:p>
                <w:tbl>
                    <w:tr>
                        <w:tc><w:p><w:r><w:t>Article 1</w:t></w:r></w:p></w:tc>
                        <w:tc><w:p><w:r><w:t>Descriptif du bien : {bien_titre}</w:t></w:r></w:p></w:tc>
                    </w:tr>
                </w:tbl>
            </w:body>
        </w:document>';

        $html = $method->invoke($importer, $xml);

        $this->assertStringContainsString('<p>CONTRAT DE BAIL HABITATION</p>', $html);
        $this->assertStringContainsString('<strong>Bailleur : {proprietaire_nom_complet}</strong>', $html);
        $this->assertStringContainsString('Descriptif du bien : {bien_titre}', $html);
        $this->assertStringContainsString('<table', $html);
    }
}
