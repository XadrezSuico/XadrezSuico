<?php

namespace App\Helper;

use App\Enxadrista;
use App\Util\Util;

class NameComparisonHelper
{
    const FORMAT_CBX = 'cbx';
    const FORMAT_FIDE = 'fide';

    const NIVEL_DIVERGENTE = 0;
    const NIVEL_PARCIAL = 1;
    const NIVEL_OK = 2;

    public static function compare($localName, $entityName, $format = self::FORMAT_CBX)
    {
        $localParts = self::parseNameParts($localName, self::FORMAT_CBX);
        $entityParts = self::parseNameParts($entityName, $format);

        if ($localParts['lastname'] === '' && $localParts['firstname'] === '') {
            return [
                'parecer' => 'Não confere',
                'nivel' => self::NIVEL_DIVERGENTE,
                'detalhe' => 'Nome local não informado.',
            ];
        }

        if ($entityParts['lastname'] === '' && $entityParts['firstname'] === '') {
            return [
                'parecer' => 'Não confere',
                'nivel' => self::NIVEL_DIVERGENTE,
                'detalhe' => 'Nome da entidade não pôde ser interpretado.',
            ];
        }

        $localFull = self::buildComparableFull($localParts);
        $entityFull = self::buildComparableFull($entityParts);

        if ($localFull === $entityFull) {
            return [
                'parecer' => 'Confere',
                'nivel' => self::NIVEL_OK,
                'detalhe' => 'Nomes normalizados idênticos.',
            ];
        }

        $localTokens = self::tokenize($localName, self::FORMAT_CBX);
        $entityTokens = self::tokenize($entityName, $format);

        if (self::tokensEquivalent($localTokens, $entityTokens)) {
            return [
                'parecer' => 'Confere',
                'nivel' => self::NIVEL_OK,
                'detalhe' => 'Conjunto de nomes equivalente após normalização.',
            ];
        }

        $localLastname = mb_strtolower($localParts['lastname']);
        $entityLastname = mb_strtolower($entityParts['lastname']);
        $localFirst = self::firstSignificantGivenName($localParts['firstname']);
        $entityFirst = self::firstSignificantGivenName($entityParts['firstname']);

        if ($localLastname !== '' && $localLastname === $entityLastname) {
            if ($localFirst !== '' && $localFirst === $entityFirst) {
                return [
                    'parecer' => 'Confere',
                    'nivel' => self::NIVEL_OK,
                    'detalhe' => 'Sobrenome e primeiro prenome coincidem.',
                ];
            }

            return [
                'parecer' => 'Verificar',
                'nivel' => self::NIVEL_PARCIAL,
                'detalhe' => 'Sobrenome coincide, mas prenomes diferem.',
            ];
        }

        $overlap = self::tokenOverlapRatio($localTokens, $entityTokens);
        $similarity = 0;
        similar_text($localFull, $entityFull, $similarity);

        if ($overlap >= 0.5 || $similarity >= 65) {
            return [
                'parecer' => 'Verificar',
                'nivel' => self::NIVEL_PARCIAL,
                'detalhe' => 'Há semelhança parcial entre os nomes.',
            ];
        }

        return [
            'parecer' => 'Não confere',
            'nivel' => self::NIVEL_DIVERGENTE,
            'detalhe' => 'Sobrenome e prenomes não correspondem.',
        ];
    }

    public static function compareEntity($enxadrista, $entityKey)
    {
        if ($entityKey === 'cbx') {
            return self::compareCbx($enxadrista);
        }

        if ($entityKey === 'fide') {
            return self::compareFide($enxadrista);
        }

        return null;
    }

    private static function compareCbx($enxadrista)
    {
        if (!$enxadrista->cbx_id || intval($enxadrista->cbx_id) <= 0) {
            return null;
        }

        if (!$enxadrista->encontrado_cbx || trim((string) $enxadrista->cbx_name) === '') {
            return [
                'id' => $enxadrista->cbx_id,
                'nome' => '-',
                'parecer' => 'Nome não integrado',
                'nivel' => self::NIVEL_DIVERGENTE,
                'detalhe' => 'ID CBX informado, mas o nome não foi obtido da CBX.',
            ];
        }

        $result = self::compare($enxadrista->name, $enxadrista->cbx_name, self::FORMAT_CBX);

        return [
            'id' => $enxadrista->cbx_id,
            'nome' => $enxadrista->cbx_name,
            'parecer' => $result['parecer'],
            'nivel' => $result['nivel'],
            'detalhe' => $result['detalhe'],
        ];
    }

    private static function compareFide($enxadrista)
    {
        if (!$enxadrista->fide_id || intval($enxadrista->fide_id) <= 0) {
            return null;
        }

        if (!$enxadrista->encontrado_fide || trim((string) $enxadrista->fide_name) === '') {
            return [
                'id' => $enxadrista->fide_id,
                'nome' => '-',
                'parecer' => 'Nome não integrado',
                'nivel' => self::NIVEL_DIVERGENTE,
                'detalhe' => 'ID FIDE informado, mas o nome não foi obtido da FIDE.',
            ];
        }

        $result = self::compare($enxadrista->name, $enxadrista->fide_name, self::FORMAT_FIDE);

        return [
            'id' => $enxadrista->fide_id,
            'nome' => $enxadrista->fide_name,
            'parecer' => $result['parecer'],
            'nivel' => $result['nivel'],
            'detalhe' => $result['detalhe'],
        ];
    }

    public static function parseNameParts($name, $format = self::FORMAT_CBX)
    {
        $name = trim((string) $name);

        if ($name === '') {
            return ['firstname' => '', 'lastname' => ''];
        }

        if ($format === self::FORMAT_FIDE && strpos($name, ',') !== false) {
            $parts = explode(',', $name, 2);

            return [
                'lastname' => Enxadrista::sanitizeForSwissManager(trim($parts[0])),
                'firstname' => Enxadrista::sanitizeForSwissManager(trim($parts[1])),
            ];
        }

        $enxadrista = new Enxadrista();
        $enxadrista->name = $name;
        $enxadrista->splitName();

        return [
            'firstname' => Enxadrista::sanitizeForSwissManager($enxadrista->firstname ?? ''),
            'lastname' => Enxadrista::sanitizeForSwissManager($enxadrista->lastname ?? ''),
        ];
    }

    public static function tokenize($name, $format = self::FORMAT_CBX)
    {
        $parts = self::parseNameParts($name, $format);
        $tokens = [];

        foreach ([$parts['firstname'], $parts['lastname']] as $part) {
            foreach (preg_split('/\s+/', trim($part)) as $token) {
                $token = mb_strtolower(trim($token));
                if ($token === '' || Util::ePreposicao(mb_strtoupper($token)) || Util::eGeracaoDeFamilia(mb_strtoupper($token))) {
                    continue;
                }
                $tokens[] = $token;
            }
        }

        return array_values(array_unique($tokens));
    }

    private static function buildComparableFull(array $parts)
    {
        return mb_strtolower(trim($parts['lastname'] . ' ' . $parts['firstname']));
    }

    private static function firstSignificantGivenName($firstname)
    {
        foreach (preg_split('/\s+/', trim((string) $firstname)) as $part) {
            $part = mb_strtolower(trim($part));
            if ($part === '' || Util::ePreposicao(mb_strtoupper($part))) {
                continue;
            }

            return $part;
        }

        return '';
    }

    private static function tokensEquivalent(array $localTokens, array $entityTokens)
    {
        if (count($localTokens) === 0 || count($entityTokens) === 0) {
            return false;
        }

        sort($localTokens);
        sort($entityTokens);

        return $localTokens === $entityTokens;
    }

    private static function tokenOverlapRatio(array $localTokens, array $entityTokens)
    {
        if (count($localTokens) === 0 || count($entityTokens) === 0) {
            return 0;
        }

        $intersection = array_intersect($localTokens, $entityTokens);
        $unionCount = count(array_unique(array_merge($localTokens, $entityTokens)));

        if ($unionCount === 0) {
            return 0;
        }

        return count($intersection) / $unionCount;
    }
}
