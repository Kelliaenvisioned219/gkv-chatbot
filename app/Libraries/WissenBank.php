<?php

namespace App\Libraries;

/**
 * WissenBank — keyword-based knowledge retrieval.
 * Selects the most relevant markdown files for a given user question.
 */
class WissenBank
{
    private array $files = [];
    private string $wissenDir;
    private int $maxChars;

    /** Keyword → file mapping */
    private array $keywordMap = [
        '01_gkv_system.md' => [
            'gkv', 'gesetzlich', 'krankenversicherung', 'system', 'solidarprinzip',
            'sgb', 'kassenart', 'aok', 'bkk', 'ikk', 'ersatzkasse', 'bundesausschuss',
            'g-ba', 'leistungskatalog', 'pflichtleistung', 'satzungsleistung',
            'selbstverwaltung', 'was ist', 'überblick', 'grundprinzip',
        ],
        '02_versicherungsarten.md' => [
            'versichert', 'pflichtversicher', 'freiwillig', 'familienversicher',
            'mitglied', 'wechsel', 'kündigung', 'sonderkündigung', 'kassenwahl',
            'beitragsfrei', 'einkommensgrenze', 'wechseln', 'kündigen', 'pkv',
            'privat', 'student', 'selbstständig', 'minijob', 'arbeitnehmer',
            'beamte', 'rentner', 'elternzeit', 'versicherungspflichtgrenze',
        ],
        '03_beitraege.md' => [
            'beitrag', 'kosten', 'preis', 'zusatzbeitrag', 'kostet',
            'beitragssatz', 'prozent', 'arbeitgeber', 'bemessungsgrenze',
            'pflegeversicherung', 'zuzahlung', 'befreiung', 'belastungsgrenze',
            'teuer', 'günstig', 'mindestbeitrag', 'beitragsberechnung',
        ],
        '04_leistungen_vorsorge.md' => [
            'leistung', 'vorsorge', 'prävention', 'impfung', 'check-up',
            'screening', 'krebs', 'untersuchung', 'arzt', 'psychotherapie',
            'arzneimittel', 'medikament', 'krankenhaus', 'rehabilitation',
            'hilfsmittel', 'hörgerät', 'rollstuhl', 'kur', 'rezept',
            'präventionskurs', 'kurs', 'sport', 'bewegung', 'ernährung',
        ],
        '05_krankengeld.md' => [
            'krankengeld', 'arbeitsunfähig', 'krank', 'lohnfortzahlung',
            'krankschreibung', 'eau', 'kinderkrankengeld', '78 wochen',
            'brutto', 'nettogehalt', 'aussteuerung',
        ],
        '06_pflege.md' => [
            'pflege', 'pflegegrad', 'pflegegeld', 'pflegeheim', 'barrierefreie',
            'wohnraum', 'kurzzeitpflege', 'verhinderungspflege', 'pflegehilfsmittel',
            'pflegeberatung', 'medizinischer dienst', 'gutachten', 'entlastungsbetrag',
        ],
        '07_zahngesundheit.md' => [
            'zahn', 'zahnreinigung', 'pzr', 'zahnersatz', 'krone', 'brücke',
            'prothese', 'bonusheft', 'festzuschuss', 'kieferorthopädie',
            'füllung', 'amalgam', 'parodontitis', 'zahnstein',
        ],
        '08_schwangerschaft_familie.md' => [
            'schwangerschaft', 'schwanger', 'baby', 'geburt', 'hebamme',
            'geburtsvorbereit', 'künstliche befruchtung', 'mutterschaftsgeld',
            'mutterpass', 'u-untersuchung', 'kind', 'familie', 'elterngeld',
            'elternzeit', 'mutterschutz',
        ],
        '09_digitale_services.md' => [
            'epa', 'patientenakte', 'elektronisch', 'digital', 'app',
            'diga', 'e-rezept', 'gesundheitskarte', 'egk', 'online',
            'portal', 'postfach',
        ],
        '10_rechte_beschwerden.md' => [
            'beschwerde', 'problem', 'unzufrieden', 'widerspruch', 'klage',
            'recht', 'patientenrecht', 'zweitmeinung', 'behandlungsfehler',
            'ablehnung', 'abgelehnt', 'genehmigungsfiktion', 'sozialgericht',
            'upd', 'patientenberatung', 'aufsichtsbehörde', 'ärger',
            'langsam', 'bearbeitungszeit', 'mahnung', 'falsche rechnung',
        ],
    ];

    public function __construct(int $maxChars = 30000)
    {
        $this->wissenDir = ROOTPATH . 'wissenBank';
        $this->maxChars = $maxChars;
        $this->loadFiles();
    }

    private function loadFiles(): void
    {
        $mdFiles = glob($this->wissenDir . '/*.md');
        sort($mdFiles);
        foreach ($mdFiles as $path) {
            $basename = basename($path);
            $this->files[$basename] = file_get_contents($path);
        }
    }

    /**
     * Select the most relevant context for a user question.
     *
     * @return array{context: string, fileCount: int}
     */
    public function selectContext(string $userMessage): array
    {
        $messageLower = mb_strtolower($userMessage, 'UTF-8');
        $scores = [];

        foreach ($this->keywordMap as $filename => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($messageLower, $keyword)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $scores[$filename] = $score;
            }
        }

        // Sort by score descending
        arsort($scores);

        $selected = [];
        $totalChars = 0;

        foreach ($scores as $filename => $score) {
            $content = $this->files[$filename] ?? '';
            if ($totalChars + strlen($content) <= $this->maxChars) {
                $selected[] = "--- {$filename} ---\n{$content}";
                $totalChars += strlen($content);
            }
        }

        // Fallback: if nothing matched, use GKV system overview + rights
        if (empty($selected)) {
            foreach (['01_gkv_system.md', '10_rechte_beschwerden.md'] as $fallback) {
                $content = $this->files[$fallback] ?? '';
                if ($totalChars + strlen($content) <= $this->maxChars) {
                    $selected[] = "--- {$fallback} ---\n{$content}";
                    $totalChars += strlen($content);
                }
            }
        }

        return [
            'context'   => implode("\n\n", $selected),
            'fileCount' => count($selected),
            'chars'     => $totalChars,
        ];
    }

    public function getFileCount(): int
    {
        return count($this->files);
    }
}
