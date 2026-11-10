<?php

namespace App\Console\Commands;

use App\Evento;
use App\Inscricao;
use Illuminate\Console\Command;

class SendEventRegistrationsToPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xadrezsuicopag:send-registrations {evento : ID do evento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia as inscricoes de um evento com integracao de pagamento para o XadrezSuicoPAG.';

    /**
     * Delay em microssegundos entre requisicoes a API de nova inscricao.
     */
    private const REGISTRATION_REQUEST_DELAY_MICROSECONDS = 300000;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $eventoId = (int) $this->argument('evento');

        /** @var Evento|null $evento */
        $evento = Evento::find($eventoId);

        if ($evento === null) {
            $this->error(__('xadrezsuicopag.send_registrations.event_not_found', ['id' => $eventoId]));

            return 1;
        }

        if (!$evento->isPaid()) {
            $this->error(__('xadrezsuicopag.send_registrations.event_not_paid', [
                'name' => $evento->nome,
                'id' => $evento->id,
            ]));

            return 1;
        }

        $inscricoes = Inscricao::query()
            ->whereHas('torneio', function ($query) use ($eventoId) {
                $query->where('evento_id', '=', $eventoId);
            })
            ->with(['enxadrista', 'torneio.evento', 'categoria'])
            ->get()
            ->filter(function (Inscricao $inscricao) {
                if ($inscricao->isFree()) {
                    return false;
                }

                return $inscricao->getPaymentInfo('uuid') === false;
            })
            ->values();

        $total = $inscricoes->count();

        if ($total === 0) {
            $this->info(__('xadrezsuicopag.send_registrations.nothing_pending', [
                'name' => $evento->nome,
                'id' => $evento->id,
            ]));

            return 0;
        }

        $this->info(__('xadrezsuicopag.send_registrations.event_line', [
            'name' => $evento->nome,
            'id' => $evento->id,
        ]));
        $this->info(__('xadrezsuicopag.send_registrations.registrations_to_send', [
            'count' => $total,
        ]));
        $this->line('');

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(
            ' %current%/%max% [%bar%] %percent:3s%% -- %message%'
        );
        $bar->setMessage(__('xadrezsuicopag.send_registrations.waiting'));
        $bar->start();

        $successCount = 0;
        $failureCount = 0;
        $processedCount = 0;

        foreach ($inscricoes as $inscricao) {
            if ($processedCount > 0) {
                usleep(self::REGISTRATION_REQUEST_DELAY_MICROSECONDS);
            }

            $playerName = $inscricao->enxadrista
                ? $inscricao->enxadrista->name
                : __('xadrezsuicopag.send_registrations.unknown_player');

            $bar->setMessage(__('xadrezsuicopag.send_registrations.processing', [
                'id' => $inscricao->id,
                'player' => $playerName,
            ]));

            $inscricao->registerToPayment();
            $inscricao->refresh();

            if ($inscricao->getPaymentInfo('uuid') !== false) {
                $successCount++;
            } else {
                $failureCount++;
            }

            $processedCount++;
            $bar->advance();
        }

        $bar->setMessage(__('xadrezsuicopag.send_registrations.done'));
        $bar->finish();
        $this->line('');
        $this->line('');

        $this->table(
            [
                __('xadrezsuicopag.send_registrations.summary'),
                __('xadrezsuicopag.send_registrations.quantity'),
            ],
            [
                [__('xadrezsuicopag.send_registrations.total'), $total],
                [__('xadrezsuicopag.send_registrations.success'), $successCount],
                [__('xadrezsuicopag.send_registrations.failure'), $failureCount],
            ]
        );

        return $failureCount > 0 ? 1 : 0;
    }
}
