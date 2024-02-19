# search_it_with_gpt - Search It als Datenquelle für eigene GPTs von OpenAI

Mit diesem Add-on können eigene GPTs über eine passende OpenAPI mit Antworten aus den Suchergebnissen von Search It ergänzt werden.

## Anforderungen

* Search It ab Version 6.10.0
* YRewrite ab Version 2.10.0
* ChatGPT von OpenAI mit Plus-Abo

Beteilige dich an der Weiterentwicklung des Addons unter <https://github.com/alexplusde/search_it_with_gpt>.

## Installation

Bei der Installation wird automatisch ein zufälliger API-Key generiert, der in der Einstellungs-Seite des Add-ons angezeigt wird. Dieser API-Key muss in der OpenAPI-Definition als Header `X-SearchItWithGpt-Token` übertragen werden.

## Einrichtung

> **Hinweis:** Derzeit werden keine URL-Suchtreffer unterstützt. Die Suche erfolgt nur über Article-Treffer der Seite.
> **Hinweis:** Die Suche erfolgt nur über die aktuelle Sprache der Website.

Erstelle ein eigenes GPT-Modell unter <https://chat.openai.com/gpts/editor> und wähle den Bereich "Configure". Dort kannst du ganz unten "Actions" hinzufügen. Erstelle eine Action mit folgenden Einstellungen:

### Authentifizierung

* API Key: `Dein API-Key aus den Add-on-Einstellungen`, die Einstellungsseite des Add-ons befindet sich in REDAXO unter `Search it > Mit GPT`.
* Custom: `X-SearchItWithGpt-Token`
* Schema: (Das nachfolgende OpenAPI-Schema unten)
* Privacy Policy: Eine passende Datenschutzseite, die den Zugriff regelt.

> **Hinweis:** Bitte beachte einen rechtskonformen Einsatz des GPT-Modells und die Einhaltung der Datenschutzbestimmungen. Diese sind möglicherweise anwaltlich zu prüfen.

Anschließend kannst du die Action testen und erhältst die passenden Antworten aus den Suchergebnissen von Search It.

## OpenAPI-Schema

```yaml
openapi: 3.0.0
info:
  title: SearchItExtensionAPI
  description: Stellt zusätzliche Informationen anhand der Website-Suchfunktion bereit.
  version: 1.0.0
servers:
  - url: https://example.org/ # URL anpassen
    description: Stellt zusätzliche Informationen der Website +++Unternehmensname+++ als Suchergebnisse zur Verfügung # Unternehmensname anpassen
paths:
  /:
    get:
      operationId: searchQuery
      summary: Führt eine Suchanfrage durch und gibt Ergebnisse zurück.
      description: Nimmt einen Suchbegriff entgegen und liefert Suchergebnisse zurück.
      parameters:
        - in: query
          name: rex-api-call
          required: true
          description: API-Funktionsaufruf, muss 'search_it_with_gpt' sein.
          schema:
            type: string
            default: search_it_with_gpt
        - in: query
          name: search
          required: true
          description: Suchbegriff, bestehend aus 1-2 Wörtern.
          schema:
            type: string
      responses:
        "200":
          description: Eine Liste von Suchergebnissen.
          content:
            application/json:
              schema:
                type: object
                properties:
                  status:
                    type: boolean
                    description: Status der Anfrage.
                  count:
                    type: integer
                    description: Anzahl der gefundenen Ergebnisse.
                  results:
                    type: array
                    items:
                      type: object
                      properties:
                        title:
                          type: string
                          description: Der Titel des Suchergebnisses.
                        url:
                          type: string
                          description: Die URL des Suchergebnisses.
                        teaser:
                          type: string
                          description: Ein kurzer Ausschnitt des Inhalts.
                        content:
                          type: string
                          description: Der gesamte Inhalt des Suchergebnisses.
components:
  securitySchemes:
    ApiKeyAuth: # Geändert von BearerAuth zu ApiKeyAuth
      type: apiKey
      in: header # Der API-Key wird im Header übertragen
      name: X-SearchItWithGpt-Token # Name des Headers
  schemas:
    SearchResult:
      type: object
      properties:
        title:
          type: string
        url:
          type: string
        teaser:
          type: string
        content:
          type: string
security:
components:
  securitySchemes:
    ApiKeyAuth: 
      type: apiKey
      in: header
      name: X-SearchItWithGpt-Token
  schemas:
    SearchResult:
      type: object
      properties:
        title:
          type: string
        url:
          type: string
        teaser:
          type: string
        content:
          type: string
security:
  - ApiKeyAuth: []
```

## Lizenz

MIT Lizenz, siehe [LICENSE.md](https://github.com/alexplusde/search_it_with_gpt/blob/master/LICENSE.md)  

## Autoren

**Alexander Walther**  
<http://www.alexplus.de>  
<https://github.com/alexplusde>  

**Projekt-Lead**  
[Alexander Walther](https://github.com/alexplusde)

## Credits
