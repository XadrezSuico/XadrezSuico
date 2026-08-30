<?php

namespace App\Http\Controllers;

use App\Enxadrista;
use App\Pais;
use App\PlayerTitle;
use App\Sexo;
use App\Title;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class FIDERatingController extends Controller
{
    public function updateRatings()
    {
        $enxadristas = Enxadrista::where([
            ["fide_id", "!=", null],
            ["fide_last_update", "<", date("Y-m") . "-01 00:00:00"],
        ])
            ->orWhere([
                ["fide_id", "!=", null],
                ["fide_last_update", "=", null],
            ])
            ->limit(5)
            ->get();
        foreach ($enxadristas as $enxadrista) {
            if($enxadrista->fide_id  > 0){
                $this->getRating($enxadrista);
            }else{
                $enxadrista->fide_id = null;
                $enxadrista->save();
            }
        }
    }

    // public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true){
    //     $codigo_organizacao = 0;


    //     if($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name;

    //     $client = new Client;
    //     $response = $client->get("http://ratings.fide.com/card.phtml?event=" . $enxadrista->fide_id);
    //     $html = (string) $response->getBody();
    //     // echo $html;

    //     $explode_not_found = explode("Player not found", $html);
    //     if (count($explode_not_found) == 1) {
    //         // continuar o desenvolvimento a partir daqui
    //         $enxadrista->encontrado_fide = true;
    //         $enxadrista->fide_name = FIDERatingController::getName($html);


    //         $explode_table_1 = explode("<table width=100% cellpadding=0 cellspacing=0 align=ceter broder=0>", $html);
    //         if (count($explode_table_1) == 2) {
    //             $explode_table_2 = explode("</table>", $explode_table_1[1]);
    //             if (count($explode_table_2) >= 2) {
    //                 $explode_table_3 = explode("<tr>", $explode_table_2[0]);
    //                 if (count($explode_table_3) == 2) {
    //                     $explode_table_4 = explode("</tr>", $explode_table_3[1]);
    //                     if (count($explode_table_4) == 2) {
    //                         $explode_columns = explode("align=center>", $explode_table_4[0]);

    //                         $std = "<small>std.</small><br>";
    //                         $rpd = "<small>rapid</small><br>";
    //                         $btz = "<small>blitz</small><br>";
    //                         foreach ($explode_columns as $column_brute) {
    //                             $column = explode("</td>", $column_brute);
    //                             if (count($column) == 2) {
    //                                 $exp_std = explode($std, $column[0]);
    //                                 $exp_rpd = explode($rpd, $column[0]);
    //                                 $exp_btz = explode($btz, $column[0]);
    //                                 $rating = Util::numeros($column[0]);

    //                                 if($show_text) echo "Rating: " . $rating;
    //                                 if (count($exp_std) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 0, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } elseif (count($exp_rpd) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 1, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } elseif (count($exp_btz) == 2) {
    //                                     if (is_int(intval($rating))) {
    //                                         if (intval($rating) > 0) {
    //                                             if($save_rating) $enxadrista->setRating($codigo_organizacao, 2, intval($rating));
    //                                         }
    //                                     } else {
    //                                         if($show_text) echo "Erro Rating não é inteiro!";
    //                                     }
    //                                 } else {
    //                                     if($show_text) echo "Erro Nenhum tipo de rating encontrado";
    //                                 }
    //                             } else {
    //                                 if($show_text) echo "Erro column";
    //                             }
    //                         }
    //                     } else {
    //                         if($show_text) echo "Erro explode_table_4";
    //                     }
    //                 } else {
    //                     if($show_text) echo "Erro explode_table_3";
    //                 }
    //             } else {
    //                 if($show_text) echo "Erro explode_table_2";
    //             }
    //         } else {
    //             if($show_text) echo "Erro explode_table_1";
    //         }
    //     }else{
    //         $enxadrista->encontrado_fide = false;
    //     }

    //     if($save_rating) $enxadrista->fide_last_update = date("Y-m-d H:i:s");
    //     if($return_enxadrista){
    //         return $enxadrista;
    //     }else{
    //         $enxadrista->save();
    //     }
    //     if($show_text) echo "<hr/>";
    // }

    // public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true)
    // {
    //     $codigo_organizacao = 0;


    //     if ($show_text) echo "Enxadrista #" . $enxadrista->id . " - " . $enxadrista->name . "(" . $enxadrista->fide_id . ")";

    //     if (!env("FIDE_RATING_SERVER", false)) {
    //         if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
    //         if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
    //         if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);


    //         if ($save_rating) $enxadrista->fide_last_update = date("Y-m-d H:i:s");

    //         if ($return_enxadrista) {
    //             return $enxadrista;
    //         } else {
    //             $enxadrista->save();
    //         }
    //     } else {
    //         $client = new Client([
    //             'http_errors' => false,
    //         ]);
    //         $response = $client->get(env("FIDE_RATING_SERVER", false) . "/player/" . $enxadrista->fide_id . "/elo");
    //         if ($show_text) echo "<br/>";

    //         if ($response->getStatusCode() != 200) {
    //             $html = "{}";
    //         } else {
    //             $html = (string) $response->getBody();
    //         }

    //         $not_found = true;

    //         $json = json_decode($html);
    //         if (!isset($json->reason)) {
    //             $enxadrista->encontrado_fide = true;
    //             if (!$return_enxadrista) $enxadrista->save();
    //             $not_found = false;
    //             if (isset($json->standard_elo)) {
    //                 if (is_numeric($json->standard_elo)) {
    //                     if ($show_text) echo "STD:" . $json->standard_elo;
    //                     if ($save_rating) $enxadrista->setRating($codigo_organizacao, 0, intval($json->standard_elo));
    //                 } else {
    //                     if ($show_text) echo "STD: String (" . $json->standard_elo . ")";
    //                     if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
    //                 }
    //             } else {
    //                 if ($show_text) echo "STD: Not Found";
    //                 if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 0);
    //             }
    //             if ($show_text) echo "<br/>";
    //             if (isset($json->rapid_elo)) {
    //                 if (is_numeric($json->rapid_elo)) {
    //                     if ($show_text) echo "RPD:" . $json->rapid_elo;
    //                     if ($save_rating) $enxadrista->setRating($codigo_organizacao, 1, intval($json->rapid_elo));
    //                 } else {
    //                     if ($show_text) echo "RPD: String (" . $json->rapid_elo . ")";
    //                     if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
    //                 }
    //             } else {
    //                 if ($show_text) echo "RPD: Not Found";
    //                 if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 1);
    //             }
    //             if ($show_text) echo "<br/>";
    //             if (isset($json->blitz_elo)) {
    //                 if (is_numeric($json->blitz_elo)) {
    //                     if ($show_text) echo "BTZ:" . $json->blitz_elo;
    //                     if ($save_rating) $enxadrista->setRating($codigo_organizacao, 2, intval($json->blitz_elo));
    //                 } else {
    //                     if ($show_text) echo "BTZ: String (" . $json->blitz_elo . ")";
    //                     if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);
    //                 }
    //             } else {
    //                 if ($show_text) echo "BTZ: Not Found";
    //                 if ($save_rating) $enxadrista->deleteRating($codigo_organizacao, 2);
    //             }
    //         } else {
    //             $enxadrista->fide_name = null;
    //             $enxadrista->encontrado_fide = false;
    //             if (!$return_enxadrista) $enxadrista->save();
    //         }



    //         if ($not_found && $save_rating) {
    //             $enxadrista->deleteRating($codigo_organizacao, 0);
    //             $enxadrista->deleteRating($codigo_organizacao, 1);
    //             $enxadrista->deleteRating($codigo_organizacao, 2);
    //         }

    //         if ($save_rating) $enxadrista->fide_last_update = date("Y-m-d H:i:s");
    //         if ($return_enxadrista) {
    //             return $enxadrista;
    //         } else {
    //             $enxadrista->save();
    //         }
    //     }
    //     if ($show_text) echo "<hr/>";
    // }
    private const FIDE_ENTITY_ID = 1;

    private const FIDE_TITLE_MAP = [
        'Grandmaster' => 'GM',
        'International Master' => 'IM',
        'FIDE Master' => 'FM',
        'Candidate Master' => 'CM',
        'Woman Grandmaster' => 'WGM',
        'Woman International Master' => 'WIM',
        'Woman FIDE Master' => 'WFM',
        'Woman Candidate Master' => 'WCM',
    ];

    public static function getRating($enxadrista, $show_text = true, $return_enxadrista = false, $save_rating = true)
    {
        $codigo_organizacao = 0;
        $url = "https://ratings.fide.com/profile/" . $enxadrista->fide_id;

        Log::debug("FIDERatingController::getRating - '{$enxadrista->fide_id}' - {$url}");
        if ($show_text) {
            echo "Enxadrista #{$enxadrista->id} - {$enxadrista->name} ({$enxadrista->fide_id})<br/>{$url}<br/>";
        }

        try {
            $client = HttpClient::create([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; XadrezSuico/1.0)',
                ],
            ]);
            $browser = new HttpBrowser($client);
            $crawler = $browser->request('GET', $url);
            $statusCode = $browser->getInternalResponse()->getStatusCode();
            $html = $browser->getInternalResponse()->getContent();

            if ($statusCode !== 200 || self::isPlayerNotFound($crawler, $html)) {
                Log::debug("FIDERatingController::getRating - '{$enxadrista->fide_id}' - jogador não encontrado (status {$statusCode})");
                if ($show_text) {
                    echo "Jogador FIDE não encontrado<br/>";
                }
                self::markNotFound($enxadrista, $codigo_organizacao, $save_rating);

                return $return_enxadrista ? $enxadrista : null;
            }

            $name = self::extractName($crawler);
            $ratings = self::extractRatings($crawler);

            Log::debug("FIDERatingController::getRating - '{$enxadrista->fide_id}' - {$name} STD:" . ($ratings['standard'] ?? 'null') . " RPD:" . ($ratings['rapid'] ?? 'null') . " BTZ:" . ($ratings['blitz'] ?? 'null'));
            if ($show_text) {
                echo "Nome FIDE: {$name}<br/>";
                echo "STD: " . ($ratings['standard'] ?? 'Not rated') . "<br/>";
                echo "RPD: " . ($ratings['rapid'] ?? 'Not rated') . "<br/>";
                echo "BTZ: " . ($ratings['blitz'] ?? 'Not rated') . "<br/>";
            }

            $enxadrista->encontrado_fide = true;
            $enxadrista->fide_name = $name;

            self::applyRatings($enxadrista, $codigo_organizacao, $ratings, $save_rating);

            if ($save_rating) {
                self::syncMetadata($enxadrista, $crawler);
                $enxadrista->fide_last_update = date("Y-m-d H:i:s");
                $enxadrista->save();
            }

            if ($return_enxadrista) {
                return $enxadrista;
            }
        } catch (\Exception $e) {
            Log::debug("FIDERatingController::getRating - '{$enxadrista->fide_id}' - erro: " . $e->getMessage());
            if ($show_text) {
                echo "Erro ao acessar a página: " . $e->getMessage() . "<br/>";
            }
            self::markNotFound($enxadrista, $codigo_organizacao, $save_rating);

            return $return_enxadrista ? $enxadrista : null;
        }

        if ($show_text) {
            echo "<hr/>";
        }
    }

    private static function isPlayerNotFound(Crawler $crawler, string $html): bool
    {
        if (stripos($html, 'No record found') !== false) {
            return true;
        }

        return $crawler->filter('h1.player-title')->count() === 0;
    }

    private static function markNotFound(Enxadrista $enxadrista, int $codigo_organizacao, bool $save_rating): void
    {
        $enxadrista->encontrado_fide = false;
        $enxadrista->fide_name = null;

        if (!$save_rating) {
            return;
        }

        $enxadrista->deleteRating($codigo_organizacao, 0);
        $enxadrista->deleteRating($codigo_organizacao, 1);
        $enxadrista->deleteRating($codigo_organizacao, 2);
        $enxadrista->fide_last_update = date("Y-m-d H:i:s");
        $enxadrista->save();
    }

    private static function extractName(Crawler $crawler): string
    {
        return trim($crawler->filter('h1.player-title')->first()->text());
    }

    private static function extractRatings(Crawler $crawler): array
    {
        $selectors = [
            'standard' => '.profile-standart.profile-game',
            'rapid' => '.profile-rapid.profile-game',
            'blitz' => '.profile-blitz.profile-game',
        ];

        $ratings = [];
        foreach ($selectors as $key => $selector) {
            $ratings[$key] = $crawler->filter($selector)->count() > 0
                ? self::parseRatingValue($crawler->filter($selector)->first())
                : null;
        }

        return $ratings;
    }

    private static function parseRatingValue(Crawler $gameNode): ?int
    {
        if ($gameNode->filter('p')->count() === 0) {
            return null;
        }

        $text = trim($gameNode->filter('p')->first()->text());
        if ($text === '' || stripos($text, 'Not rated') !== false) {
            return null;
        }

        return is_numeric($text) ? (int) $text : null;
    }

    private static function applyRatings(Enxadrista $enxadrista, int $codigo_organizacao, array $ratings, bool $save_rating): void
    {
        $modalidades = [
            'standard' => 0,
            'rapid' => 1,
            'blitz' => 2,
        ];

        foreach ($modalidades as $key => $modalidade) {
            if (!$save_rating) {
                continue;
            }

            if (($ratings[$key] ?? null) !== null) {
                $enxadrista->setRating($codigo_organizacao, $modalidade, $ratings[$key]);
            } else {
                $enxadrista->deleteRating($codigo_organizacao, $modalidade);
            }
        }
    }

    private static function syncMetadata(Enxadrista $enxadrista, Crawler $crawler): void
    {
        self::syncFideTitle($enxadrista, self::extractText($crawler, '.profile-info-title p'));

        if ($enxadrista->pais_id === null) {
            $federation = self::extractText($crawler, '.profile-info-country');
            if ($federation !== null) {
                $pais = Pais::where('nome_ingles', $federation)->first();
                if ($pais) {
                    $enxadrista->pais_id = $pais->id;
                }
            }
        }

        if ($enxadrista->born === null) {
            $birthYear = self::extractText($crawler, '.profile-info-byear');
            if ($birthYear !== null && is_numeric($birthYear)) {
                $enxadrista->born = intval($birthYear) . '-01-01';
            }
        }

        if ($enxadrista->sexos_id === null) {
            $sex = self::extractText($crawler, '.profile-info-sex');
            if ($sex !== null) {
                $sexo = Sexo::where('sex_from_import', $sex)->first();
                if (!$sexo && $sex === 'Male') {
                    $sexo = Sexo::where('abbr', 'M')->first();
                } elseif (!$sexo && $sex === 'Female') {
                    $sexo = Sexo::where('abbr', 'F')->first();
                }
                if ($sexo) {
                    $enxadrista->sexos_id = $sexo->id;
                }
            }
        }
    }

    private static function extractText(Crawler $crawler, string $selector): ?string
    {
        if ($crawler->filter($selector)->count() === 0) {
            return null;
        }

        $text = trim($crawler->filter($selector)->first()->text());
        return $text === '' ? null : $text;
    }

    private static function syncFideTitle(Enxadrista $enxadrista, ?string $fideTitleText): void
    {
        $fideTitleText = $fideTitleText !== null ? trim($fideTitleText) : null;

        if ($fideTitleText === null || strcasecmp($fideTitleText, 'None') === 0) {
            $enxadrista->titles()->whereHas('title', function ($query) {
                $query->where('entities_id', self::FIDE_ENTITY_ID);
            })->delete();
            return;
        }

        $abbr = self::FIDE_TITLE_MAP[$fideTitleText] ?? null;
        if ($abbr === null) {
            Log::debug("FIDERatingController::syncFideTitle - título FIDE não mapeado: {$fideTitleText}");
            return;
        }

        $title = Title::where([
            ['entities_id', '=', self::FIDE_ENTITY_ID],
            ['abbr', '=', $abbr],
        ])->first();

        if (!$title) {
            Log::debug("FIDERatingController::syncFideTitle - título não encontrado no banco: {$abbr}");
            return;
        }

        $enxadrista->titles()->whereHas('title', function ($query) use ($title) {
            $query->where('entities_id', self::FIDE_ENTITY_ID)
                ->where('id', '!=', $title->id);
        })->delete();

        if ($enxadrista->titles()->where('titles_id', $title->id)->count() === 0) {
            $playerTitle = new PlayerTitle;
            $playerTitle->players_id = $enxadrista->id;
            $playerTitle->titles_id = $title->id;
            $playerTitle->save();
        }
    }

    private static function getName($html){
        $explode = explode("<td bgcolor=#efefef width=230 height=20>",$html);
        if(count($explode) > 1){
            $explode_2 = explode("</td>",$explode[1]);
            if(count($explode_2) > 1){
                $explode_3 = explode("&nbsp;",$explode_2[0]);
                if(count($explode_3) > 1){
                    return trim($explode_3[1]);
                }else{
                    return trim($explode_2[0]);
                }
            }
        }
        return NULL;
    }

}
