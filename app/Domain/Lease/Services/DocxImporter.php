<?php

namespace App\Domain\Lease\Services;

use ZipArchive;

class DocxImporter
{
    /**
     * Extrait le contenu HTML formaté à partir d'un fichier Word (.docx).
     */
    public function convertDocxToHtml(string $filePath): string
    {
        if (! file_exists($filePath)) {
            throw new \InvalidArgumentException("Fichier introuvable : {$filePath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \InvalidArgumentException("Impossible d'ouvrir le fichier Word (.docx).");
        }

        $xmlContent = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! $xmlContent) {
            throw new \InvalidArgumentException("Contenu Word invalide (word/document.xml introuvable).");
        }

        return $this->parseXmlToHtml($xmlContent);
    }

    protected function parseXmlToHtml(string $xmlContent): string
    {
        // Supprimer proprement les espaces de noms XML sans erreur de modificateur regex
        $cleanXml = preg_replace('#<(/?)[\w-]+:#i', '<$1', $xmlContent);
        
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($cleanXml, LIBXML_NOBLANKS);
        libxml_clear_errors();

        $htmlOutput = '';

        $bodyNodes = $dom->getElementsByTagName('body');
        if ($bodyNodes->length === 0) {
            return '<p>Document vide.</p>';
        }

        $body = $bodyNodes->item(0);

        foreach ($body->childNodes as $node) {
            if ($node->nodeName === 'p') {
                $htmlOutput .= $this->parseParagraph($node);
            } elseif ($node->nodeName === 'tbl') {
                $htmlOutput .= $this->parseTable($node);
            }
        }

        return trim($htmlOutput) ?: '<p>Document vide.</p>';
    }

    protected function parseParagraph(\DOMNode $node): string
    {
        $text = '';
        $isHeading = false;
        $headingLevel = 2;
        $align = '';

        // Style & Alignement du paragraphe
        $pPrNodes = $node->getElementsByTagName('pPr');
        if ($pPrNodes->length > 0) {
            $pPr = $pPrNodes->item(0);
            
            // Alignement (jc: center, right, justify, left)
            $jcNodes = $pPr->getElementsByTagName('jc');
            if ($jcNodes->length > 0) {
                $val = strtolower($jcNodes->item(0)->getAttribute('val'));
                if (in_array($val, ['center', 'right', 'justify'])) {
                    $align = $val;
                }
            }

            // Headings / Titres
            $pStyleNodes = $pPr->getElementsByTagName('pStyle');
            if ($pStyleNodes->length > 0) {
                $val = $pStyleNodes->item(0)->getAttribute('val');
                if (stripos($val, 'Heading1') !== false || stripos($val, 'Title') !== false) {
                    $isHeading = true;
                    $headingLevel = 1;
                } elseif (stripos($val, 'Heading') !== false) {
                    $isHeading = true;
                    $headingLevel = 2;
                }
            }
        }

        foreach ($node->childNodes as $child) {
            if ($child->nodeName === 'r') {
                $text .= $this->parseRun($child);
            }
        }

        $text = trim($text);
        if (empty($text)) {
            return '<p><br></p>';
        }

        $styleAttr = $align ? " style=\"text-align: {$align};\"" : '';

        if ($isHeading) {
            return "<h{$headingLevel}{$styleAttr}>{$text}</h{$headingLevel}>";
        }

        return "<p{$styleAttr}>{$text}</p>";
    }

    protected function parseRun(\DOMNode $node): string
    {
        $runText = '';
        $isBold = false;
        $isItalic = false;
        $isUnderline = false;
        $isStrike = false;

        // Styles du texte
        $rPrNodes = $node->getElementsByTagName('rPr');
        if ($rPrNodes->length > 0) {
            $rPr = $rPrNodes->item(0);
            if ($rPr->getElementsByTagName('b')->length > 0) {
                $isBold = true;
            }
            if ($rPr->getElementsByTagName('i')->length > 0) {
                $isItalic = true;
            }
            if ($rPr->getElementsByTagName('u')->length > 0) {
                $isUnderline = true;
            }
            if ($rPr->getElementsByTagName('strike')->length > 0) {
                $isStrike = true;
            }
        }

        // Saut de ligne <br/>
        if ($node->getElementsByTagName('br')->length > 0) {
            $runText .= '<br>';
        }

        // Contenu textuel <t>
        $tNodes = $node->getElementsByTagName('t');
        foreach ($tNodes as $t) {
            $runText .= htmlspecialchars($t->nodeValue, ENT_QUOTES, 'UTF-8');
        }

        if (empty($runText) && strpos($runText, '<br>') === false) {
            return '';
        }

        if ($isBold) {
            $runText = "<strong>{$runText}</strong>";
        }
        if ($isItalic) {
            $runText = "<em>{$runText}</em>";
        }
        if ($isUnderline) {
            $runText = "<u>{$runText}</u>";
        }
        if ($isStrike) {
            $runText = "<s>{$runText}</s>";
        }

        return $runText;
    }

    protected function parseTable(\DOMNode $node): string
    {
        $tableHtml = '<table border="1" cellpadding="6" cellspacing="0" style="width:100%; border-collapse:collapse; margin:1rem 0;">';
        
        $trNodes = $node->getElementsByTagName('tr');
        foreach ($trNodes as $tr) {
            $tableHtml .= '<tr>';
            $tcNodes = $tr->getElementsByTagName('tc');
            foreach ($tcNodes as $tc) {
                $cellText = '';
                $pNodes = $tc->getElementsByTagName('p');
                foreach ($pNodes as $p) {
                    $cellText .= $this->parseParagraph($p);
                }
                $tableHtml .= '<td style="border:1px solid #cbd5e1; padding:8px;">' . $cellText . '</td>';
            }
            $tableHtml .= '</tr>';
        }

        $tableHtml .= '</table>';
        return $tableHtml;
    }
}
