<p align="center">
  <img src="docs/screenshot.png" alt="GKV Assistent — Screenshot" width="720">
</p>

<h1 align="center">GKV Assistent</h1>

<p align="center">
  <strong>KI-gestützter Ratgeber zur gesetzlichen Krankenversicherung in Deutschland</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Sprache-Deutsch-000?style=flat-square" alt="Deutsch">
  <img src="https://img.shields.io/badge/PHP-8.2+-000?style=flat-square" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Framework-CodeIgniter_4.7-000?style=flat-square" alt="CodeIgniter 4.7">
  <img src="https://img.shields.io/badge/LLM-AWS_Bedrock-000?style=flat-square" alt="AWS Bedrock">
  <img src="https://img.shields.io/badge/Lizenz-MIT-000?style=flat-square" alt="MIT Lizenz">
</p>

---

## Überblick

Der **GKV Assistent** ist ein KI-Chatbot, der Fragen zur gesetzlichen Krankenversicherung (GKV) in Deutschland beantwortet. Er nutzt eine kuratierte Wissensdatenbank auf Basis von **SGB V** und **SGB XI** und generiert Antworten ausschließlich anhand dieser Fakten — ohne Halluzinationen.

**Kernprinzipien:**

- ⬛ **Kassenunabhängig** — Vertritt keine bestimmte Krankenkasse
- ⬛ **Faktenbasiert** — Antworten nur auf Basis der bereitgestellten Wissensbasis
- ⬛ **Keine erfundenen URLs** — Generiert keine Links oder Kontaktdaten
- ⬛ **Datenschutzkonform** — Keine Speicherung von Nutzerdaten

---

## Funktionen

| Funktion | Beschreibung |
|---|---|
| **Keyword-Retrieval** | Relevante Wissensdateien werden automatisch anhand der Nutzerfrage ausgewählt |
| **LLM-Antworten** | AWS Bedrock (Amazon Nova Pro) generiert Antworten auf Basis der Wissensbasis |
| **SSE-Streaming** | Antworten werden zeichenweise gestreamt (Typing-Effekt) |
| **Spracheingabe** | Browser-native Speech-to-Text (Deutsch) |
| **Sprachausgabe** | Text-to-Speech für Bot-Antworten |
| **Wissenbank-Viewer** | Interaktive Übersicht aller Wissensdateien |
| **Sicherheitsanalyse** | Governance & Safety Dokumentation |

---

## Projektstruktur

```
gkv-chatbot/
│
├── app/
│   ├── Controllers/
│   │   ├── BaseController.php        # Basis-Controller
│   │   ├── Chat.php                  # ◼ Chat-API (Systemprompt, SSE-Stream)
│   │   └── Home.php                  # Startseite → chat View
│   │
│   ├── Libraries/
│   │   ├── BedrockClient.php         # ◼ AWS Bedrock Client (cURL + SigV4)
│   │   └── WissenBank.php            # ◼ Keyword-basierte Kontextauswahl
│   │
│   ├── Views/
│   │   └── chat.php                  # ◼ Frontend (HTML/CSS/JS)
│   │
│   └── Config/
│       ├── Routes.php                #   API-Routen
│       └── ...                       #   CodeIgniter Konfiguration
│
├── wissenBank/                       # ◼ Wissensdatenbank (10 Markdown-Dateien)
│   ├── 01_gkv_system.md              #   GKV-System, SGB V, Kassenarten
│   ├── 02_versicherungsarten.md      #   Pflicht-/Familien-/freiwillige Versicherung
│   ├── 03_beitraege.md               #   Beitragssätze, Grenzen, Zuzahlungen
│   ├── 04_leistungen_vorsorge.md     #   Pflichtleistungen, Vorsorge, Prävention
│   ├── 05_krankengeld.md             #   Krankengeld, eAU, Kinderkrankengeld
│   ├── 06_pflege.md                  #   Pflegegrade, Pflegegeld, SGB XI
│   ├── 07_zahngesundheit.md          #   Zahnersatz, Bonusheft, Kieferorthopädie
│   ├── 08_schwangerschaft_familie.md #   Schwangerschaft, U-Untersuchungen
│   ├── 09_digitale_services.md       #   ePA, DiGA, E-Rezept, eGK
│   └── 10_rechte_beschwerden.md      #   Widerspruch, Patientenrechte, UPD
│
├── public/
│   ├── index.php                     #   Einstiegspunkt
│   ├── wissenbank.html               #   Wissenbank-Viewer
│   └── gov_safety.html               #   Sicherheitsanalyse
│
├── docs/
│   └── screenshot.png                #   Screenshot für README
│
├── .env.example                      #   Beispiel-Konfiguration
├── composer.json                     #   PHP-Abhängigkeiten
└── README.md                         #   Diese Datei
```

---

## Architektur

```mermaid
flowchart TB
    subgraph CLIENT["Browser"]
        UI["Chat-Interface<br><em>HTML / CSS / JS</em>"]
        STT["Spracheingabe<br><em>Web Speech API</em>"]
        TTS["Sprachausgabe<br><em>Web Speech API</em>"]
    end

    subgraph SERVER["CodeIgniter 4"]
        CHAT["Chat Controller<br><em>POST /api/chat</em>"]
        WB["WissenBank<br><em>Keyword-Matching</em>"]
        BC["BedrockClient<br><em>cURL + SigV4</em>"]
    end

    subgraph WISSEN["Wissensdatenbank"]
        MD["10 × Markdown<br><em>~30 KB · SGB V/XI</em>"]
    end

    subgraph AWS["AWS Bedrock"]
        LLM["Amazon Nova Pro<br><em>eu-central-1</em>"]
    end

    UI -- "Frage (JSON)" --> CHAT
    STT -.-> UI
    CHAT -- "selectContext()" --> WB
    WB -- "liest" --> MD
    WB -- "Kontext" --> CHAT
    CHAT -- "Systemprompt + Kontext + Frage" --> BC
    BC -- "HTTPS + SigV4" --> LLM
    LLM -- "Antwort" --> BC
    BC -- "Text" --> CHAT
    CHAT -- "SSE Stream" --> UI
    UI -.-> TTS

    style CLIENT fill:#f8f8f8,stroke:#000,color:#000
    style SERVER fill:#f0f0f0,stroke:#000,color:#000
    style WISSEN fill:#e8e8e8,stroke:#000,color:#000
    style AWS fill:#e0e0e0,stroke:#000,color:#000
```

---

## Antwort-Pipeline

```mermaid
flowchart LR
    A["Nutzerfrage"] --> B["Keyword-<br>Analyse"]
    B --> C["Kontext-<br>Auswahl"]
    C --> D["Prompt-<br>Erstellung"]
    D --> E["LLM-<br>Anfrage"]
    E --> F["SSE-<br>Streaming"]

    style A fill:#000,stroke:#000,color:#fff
    style B fill:#222,stroke:#000,color:#fff
    style C fill:#444,stroke:#000,color:#fff
    style D fill:#666,stroke:#000,color:#fff
    style E fill:#888,stroke:#000,color:#fff
    style F fill:#aaa,stroke:#000,color:#000
```

**Ablauf im Detail:**

1. **Keyword-Analyse** — Die Nutzerfrage wird in Kleinbuchstaben analysiert und mit dem Keyword-Index abgeglichen
2. **Kontext-Auswahl** — Die relevantesten Wissensdateien werden nach Score sortiert ausgewählt (max. 30.000 Zeichen)
3. **Prompt-Erstellung** — Systemprompt + Wissensbasis-Kontext + Chatverlauf werden zusammengesetzt
4. **LLM-Anfrage** — AWS Bedrock wird via SigV4-signiertem HTTPS-Request angefragt
5. **SSE-Streaming** — Die Antwort wird in 8-Byte-Blöcken per Server-Sent Events gestreamt

---

## Wissensbasis

Die Wissensdatenbank besteht aus **10 kuratierten Markdown-Dateien** mit insgesamt ~30 KB Inhalt. Alle Informationen basieren auf den gesetzlichen Grundlagen (SGB V, SGB XI) und sind kassenunabhängig.

| # | Datei | Thema | Schlüsselwörter |
|---|---|---|---|
| 01 | `gkv_system` | GKV-Überblick | Solidarprinzip, G-BA, Kassenarten |
| 02 | `versicherungsarten` | Mitgliedschaft | Pflicht, freiwillig, Familie, PKV |
| 03 | `beitraege` | Beitragssätze | Zusatzbeitrag, Grenzen, Zuzahlungen |
| 04 | `leistungen_vorsorge` | Leistungen | Vorsorge, Impfungen, Hilfsmittel |
| 05 | `krankengeld` | Krankengeld | eAU, 78 Wochen, Kinderkrankengeld |
| 06 | `pflege` | Pflegeversicherung | Pflegegrade, Pflegegeld, MD |
| 07 | `zahngesundheit` | Zahngesundheit | Festzuschuss, Bonusheft, PZR |
| 08 | `schwangerschaft_familie` | Familie | Mutterschaftsgeld, U-Untersuchungen |
| 09 | `digitale_services` | Digitales | ePA, DiGA, E-Rezept, eGK |
| 10 | `rechte_beschwerden` | Beschwerden | Widerspruch, UPD, BAS, Sozialgericht |

---

## Installation

### Voraussetzungen

- **PHP 8.2+** mit cURL-Erweiterung
- **Composer**
- **AWS-Konto** mit Zugriff auf Amazon Bedrock (Region `eu-central-1`)

### Einrichtung

```bash
# Repository klonen
git clone https://github.com/mhmdgazzar/gkv-chatbot.git
cd gkv-chatbot

# Abhängigkeiten installieren
composer install

# Konfiguration erstellen
cp .env.example .env

# .env bearbeiten: AWS-Zugangsdaten eintragen
nano .env

# Entwicklungsserver starten
php spark serve --port 8080
```

Anschließend im Browser öffnen: **http://localhost:8080**

---

## Konfiguration

Alle Einstellungen werden über die `.env`-Datei gesteuert:

```env
# AWS Bedrock Zugangsdaten
AWS_ACCESS_KEY_ID     = YOUR_KEY
AWS_SECRET_ACCESS_KEY = YOUR_SECRET
AWS_REGION            = eu-central-1
BEDROCK_MODEL_ID      = eu.amazon.nova-pro-v1:0
```

---

## API-Endpunkte

| Methode | Pfad | Beschreibung |
|---|---|---|
| `POST` | `/api/chat` | Chat-Nachricht senden (SSE-Response) |
| `GET` | `/api/health` | Systemstatus prüfen |
| `GET` | `/api/wissen/{datei}` | Wissensdatei lesen |

---

## Technologie

| Komponente | Technologie |
|---|---|
| Backend | PHP 8.2 · CodeIgniter 4.7 |
| LLM | AWS Bedrock · Amazon Nova Pro |
| Authentifizierung | AWS SigV4 (eigene Implementierung) |
| Frontend | Vanilla HTML · CSS · JavaScript |
| Schriftart | Inter (Google Fonts) |
| Spracherkennung | Web Speech API (Browser-nativ) |

---

## Lizenz

Dieses Projekt steht unter der [MIT-Lizenz](LICENSE).

