# search_it_with_gpt - Search It als Datenquelle für eigene GPTs von OpenAI

## Features

## OpenAPI-Schema

```yaml
openapi: 3.0.0
info:
  title: SearchItExtensionAPI
  description: Stellt zusätzliche Informationen anhand der Kundenwebsite-Suchfunktion bereit.
  version: 1.0.0
servers:
  - url: https://example.org/ # Hier die URL der Website eintragen
    description: Website # Hier den Betreiber der Website eintragen
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
          name: q
          required: true
          description: Suchbegriff, bestehend aus 1-2 Wörtern.
          schema:
            type: string
      responses:
        '200':
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
```

### Einstellungs-Seite

Beginne mit einem Konfigurations-Formular, das bereits best practice in REDAXO umsetzt - mit Links zu den wichtigsten API-Docs.

## Lizenz

MIT Lizenz, siehe [LICENSE.md](https://github.com/alexplusde/search_it_with_gpt/blob/master/LICENSE.md)  

## Autoren

**Alexander Walther**  
<http://www.alexplus.de>  
<https://github.com/alexplusde>  

**Projekt-Lead**  
[Alexander Walther](https://github.com/alexplusde)

## Credits
