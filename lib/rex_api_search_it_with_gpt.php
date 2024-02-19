<?php

use rex;
use rex_api_function;
use rex_article;
use rex_article_content;
use rex_config;
use rex_yrewrite;
use rex_yrewrite_seo;
use search_it;

class rex_api_search_it_with_gpt extends rex_api_function
{
    protected $published = true; // Erlaubt den Aufruf aus dem Frontend

    public function execute()
    {
        rex::setProperty('debug', true);

        if (!rex_server('HTTP_X_SEARCHITWITHGPT_TOKEN')) {
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authentifizierung erforderlich';
            exit;
        }

        // Extrahieren des Tokens aus dem X-SearchItWithGpt-Token Header
        $token = rex_server('HTTP_X_SEARCHITWITHGPT_TOKEN');

        if ($token !== rex_config::get('search_it_with_gpt', 'token')) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Ungültiger API-Key';
            exit;
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

                if ($article instanceof rex_article) {
                    $yrewrite = new rex_yrewrite_seo($article->getId());
                    $articleContent = new rex_article_content($article->getId());
                    $content = $articleContent->getArticle();
                    $formattedResults[] = [
                        'title' => $yrewrite->getTitle(),
                        'url' => rex_yrewrite::getFullPath(ltrim(rex_getUrl($hit['fid'], $hit['clang']), '/')),
                        'teaser' => $hit['highlightedtext'],
                        'content' => $hit['plaintext'],
                    ];
                }
            }
            /*
            if ('url' == $hit['type'] && rex_request::get('url', 'string', false)) {
                $article = rex_article::get($hit['fid']);

                // url hits
                $url_sql = rex_sql::factory();
                $url_sql->setTable(search_it_getUrlAddOnTableName());
                $url_sql->setWhere(['url_hash' => $hit['fid']]);
                if ($url_sql->select('article_id, clang_id, profile_id, data_id, seo')) {
                    if ($url_sql->getRows() > 0) {
                        $hit_server = $server;
                        if (rex_addon::get('yrewrite')->isAvailable()) {
                            $hit_domain = rex_yrewrite::getDomainByArticleId($url_sql->getValue('article_id'), $url_sql->getValue('clang_id'));
                            $hit_server = rtrim($hit_domain->getUrl(), '/');
                        }

                        $formattedResults[] = [
                            'title' => $url_info['title'],
                            'url' => $hit_link,
                            'teaser' => $hit['highlightedtext'],
                            'content' => $content,
                        ];
                    }
                } else {
                    continue;
                }
            }
            */
        }

        return $formattedResults;
    }
}
