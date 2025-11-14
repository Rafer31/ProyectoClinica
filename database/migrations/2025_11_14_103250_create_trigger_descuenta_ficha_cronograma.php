<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerDescuentaFichaCronograma extends Migration
{
    public function up()
    {
        DB::unprepared("
            CREATE TRIGGER trg_descuenta_ficha_cronograma
            AFTER INSERT ON Servicio
            FOR EACH ROW
            BEGIN
                -- Solo aplicar si tiene número de ficha y pertenece a un cronograma existente
                IF NEW.nroFicha IS NOT NULL AND NEW.fechaCrono IS NOT NULL THEN

                    -- Si es asegurado regular o no asegurado regular => se descuenta 1 ficha
                    IF NEW.tipoAseg IN ('AsegRegular', 'NoAsegRegular') THEN
                        UPDATE CronogramaAtencion
                        SET cantDispo = cantDispo - 1
                        WHERE fechaCrono = NEW.fechaCrono
                          AND cantDispo > 0; -- evitar valores negativos
                    END IF;

                    -- Si es emergencia => aumenta contador de emergencias
                    IF NEW.tipoAseg IN ('AsegEmergencia', 'NoAsegEmergencia') THEN
                        UPDATE CronogramaAtencion
                        SET cantEmergencia = cantEmergencia + 1
                        WHERE fechaCrono = NEW.fechaCrono;
                    END IF;

                END IF;
            END
        ");
    }

    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_descuenta_ficha_cronograma");
    }
}
