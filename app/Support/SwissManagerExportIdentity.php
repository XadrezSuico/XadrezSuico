<?php

namespace App\Support;

use App\Enxadrista;
use App\Inscricao;

class SwissManagerExportIdentity
{
    public static function forRegistration(Inscricao $registration, $exportType)
    {
        $type = (int) $exportType;
        $player = $registration->enxadrista;
        $club = $registration->clube;

        if (in_array($type, [1, 7], true)) {
            $name = $player->getSwissManagerFideName()['full'];
        } else {
            if (!$player->firstname || !$player->lastname) {
                $player->splitName();
            }
            if (in_array($type, [3, 4, 5, 6], true)) {
                $name = $player->firstname . ',' . $player->lastname;
                if ($type === 4) {
                    $name .= ' [' . $player->chess_com_username . ']';
                }
            } else {
                $name = $player->lastname . ',' . $player->firstname;
            }
        }

        switch ($type) {
            case 1:
                $clubNumber = $club ? $club->id : '';
                $clubName = Enxadrista::sanitizeForSwissManager($registration->cidade->name);
                if ($club) {
                    $clubName .= ' - ' . Enxadrista::sanitizeForSwissManager($club->getName());
                }
                break;
            case 7:
                $clubNumber = $club ? $club->id : '';
                $clubName = $club ? Enxadrista::sanitizeForSwissManager($club->getName()) : '';
                break;
            case 5:
                $clubNumber = 0;
                $clubName = $club ? $club->getName() : '';
                break;
            case 6:
                $clubNumber = $club ? $club->id : 0;
                $clubName = $club ? $club->getFullName() : '';
                break;
            default:
                $clubNumber = $registration->cidade->ibge_id;
                $clubName = $registration->cidade->name;
                if ($club) {
                    $clubName .= ' - ' . $club->getName();
                }
                break;
        }

        return ['name' => $name, 'club_number' => $clubNumber, 'club_name' => $clubName];
    }
}
