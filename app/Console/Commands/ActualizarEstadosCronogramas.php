<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronogramaAtencion;
use Carbon\Carbon;

class ActualizarEstadosCronogramas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronogramas:actualizar-estados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los estados de los cronogramas según la fecha actual';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando actualización de estados de cronogramas...');

        $hoy = Carbon::today()->format('Y-m-d');
        $actualizados = 0;

        // Obtener todos los cronogramas
        $cronogramas = CronogramaAtencion::all();

        foreach ($cronogramas as $cronograma) {
            $estadoAnterior = $cronograma->estado;
            $nuevoEstado = null;

            // Determinar el nuevo estado según la fecha
            if ($cronograma->fechaCrono == $hoy) {
                $nuevoEstado = 'activo';
            } elseif ($cronograma->fechaCrono > $hoy) {
                $nuevoEstado = 'inactivoFut';
            } else {
                $nuevoEstado = 'inactivoPas';
            }

            // Actualizar solo si cambió el estado
            if ($estadoAnterior !== $nuevoEstado) {
                $cronograma->estado = $nuevoEstado;
                $cronograma->save();
                $actualizados++;

                $this->line("✓ Cronograma {$cronograma->fechaCrono}: {$estadoAnterior} → {$nuevoEstado}");
            }
        }

        if ($actualizados > 0) {
            $this->info("✓ Se actualizaron {$actualizados} cronograma(s)");
        } else {
            $this->info("✓ No hay cronogramas que actualizar");
        }

        return Command::SUCCESS;
    }
}
