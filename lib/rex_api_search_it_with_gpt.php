<?php

class rex_api_search_it_with_gpt extends rex_api_function
{
    protected $published = true; // Erlaubt den Aufruf aus dem Frontend

    public function execute()
    {
        if (!rex_server('HTTP_AUTHORIZATION')) {
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authentifizierung erforderlich';
            exit;
        } else {
            // Extrahieren des Tokens aus dem Authorization Header
            list($tokenType, $token) = explode(' ', rex_server('HTTP_AUTHORIZATION'), 2);
            if (strcasecmp($tokenType, 'Bearer') != 0 || $token !== rex_config::get('search_it_with_gpt', 'token')) {
                header('HTTP/1.0 403 Forbidden');
                echo 'Ungültiger API-Key';
                exit;
            }
        }
        
        // Sicherstellen, dass der Content-Type Header gesetzt ist
        header('Content-Type: application/json; charset=UTF-8');

        // Suchanfrage über GET/POST Parameter 'search' erhalten
        $request = rex_request('search', 'string', null);

        // Prüfen, ob eine Suchanfrage vorhanden ist
        if (null === $request) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => false, 'message' => 'Suchbegriff fehlt.']);
            exit;
        }

        // Suche initialisieren und ausführen
        $search_it = new search_it();
        $result = $search_it->search($request);

        // Prüfen, ob Suchergebnisse vorhanden sind
        if ($result['count'] > 0) {
            $formattedResults = $this->formatResults($result['hits']);

            echo json_encode([
                'status' => true,
                'count' => $result['count'],
                'results' => $formattedResults,
            ]);
            exit;
        }
        // Keine Ergebnisse gefunden
        echo json_encode(['status' => true, 'count' => 0, 'message' => 'Keine Ergebnisse gefunden.']);
        exit;
    }

    private function formatResults($hits)
    {
        $formattedResults = [];

        foreach ($hits as $hit) {
            if ('article' == $hit['type']) {
                $article = rex_article::get($hit['fid']);
                $articleContent = new rex_article_content($article->getId());
                $content = $articleContent->getArticle(1); // 1 ist die ID des Slices, den Sie abrufen möchten
        
                if ($article instanceof rex_article) {
                    $formattedResults[] = [
                        'title' => $hit['title'],
                        'url' => $hit['url'],
                        'teaser' => $hit['highlightedtext'],
                        'content' => $content
                    ];
                }
            }
            if ('url' == $hit['type']) {
                // Extrahieren der Artikel-ID aus der URL
                $article = rex_article::get($hit['fid']);

                if ($article instanceof rex_article) {
                    $articleContent = new rex_article_content($article->getId());
                    $content = $articleContent->getArticle(1); // 1 ist die ID des Slices, den Sie abrufen möchten
            
                    $formattedResults[] = [
                        'title' => $hit['title'],
                        'url' => $hit['url'],
                        'teaser' => $hit['highlightedtext'],
                        'content' => $content
                    ];
                }
            }
            if ('url' == $hit['type']) {
                // Erstellen eines rex_socket Objekts für die URL
                $socket = rex_socket::factoryUrl($hit['url']);
            
                // Senden einer GET-Anfrage an die URL
                $response = $socket->doGet();
            
                // Überprüfen, ob die Anfrage erfolgreich war
                if ($response->isSuccessful()) {
                    // Abrufen des Inhalts aus der Antwort
                    $content = $response->getBody();
            
                    $formattedResults[] = [
                        'title' => $hit['title'],
                        'url' => $hit['url'],
                        'teaser' => $hit['highlightedtext'],
                        'content' => $content
                    ];
                } else {
                    continue;
                }
            }
            
        }

        return $formattedResults;
    }
}
