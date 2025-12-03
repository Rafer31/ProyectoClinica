<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero actualizar los cronogramas existentes
        DB::statement("
            UPDATE CronogramaAtencion
            SET estado = 'inactivoPasado'
            WHERE fechaCrono < CURDATE()
            AND estado != 'inactivoPasado'
        ");

        DB::statement("
            UPDATE CronogramaAtencion
            SET estado = 'activo'
            WHERE fechaCrono = CURDATE()
            AND estado = 'inactivoFut'
        ");

        // Crear trigger para INSERT (comillas simples)
        DB::unprepared("
            CREATE TRIGGER actualizar_estado_cronograma_insert
            BEFORE INSERT ON CronogramaAtencion
            FOR EACH ROW
            BEGIN
                IF NEW.fechaCrono < CURDATE() THEN
                    SET NEW.estado = 'inactivoPasado';
                ELSEIF NEW.fechaCrono = CURDATE() THEN
                    SET NEW.estado = 'activo';
                ELSEIF NEW.fechaCrono > CURDATE() THEN
                    SET NEW.estado = 'inactivoFut';
                END IF;
            END
        ");

        // Crear trigger para UPDATE (comillas simples)
        DB::unprepared("
            CREATE TRIGGER actualizar_estado_cronograma_update
            BEFORE UPDATE ON CronogramaAtencion
            FOR EACH ROW
            BEGIN
                IF NEW.fechaCrono < CURDATE() THEN
                    SET NEW.estado = 'inactivoPasado';
                ELSEIF NEW.fechaCrono = CURDATE() THEN
                    SET NEW.estado = 'activo';
                ELSEIF NEW.fechaCrono > CURDATE() THEN
                    SET NEW.estado = 'inactivoFut';
                END IF;
            END
        ");

        // Crear evento diario (comillas simples)
      DB::unprepared("
    CREATE EVENT IF NOT EXISTS actualizar_estados_cronograma_event
    ON SCHEDULE EVERY 1 MINUTE
    ON COMPLETION PRESERVE
    ENABLE
    DO
    BEGIN
        UPDATE CronogramaAtencion
        SET estado = 'inactivoPasado'
        WHERE fechaCrono < CURDATE()
        AND estado != 'inactivoPasado';

        UPDATE CronogramaAtencion
        SET estado = 'activo'
        WHERE fechaCrono = CURDATE()
        AND estado = 'inactivoFut';
    END
");

        // Habilitar event scheduler
        DB::unprepared('SET GLOBAL event_scheduler = ON');
    }

    public function down(): void
    {
        DB::unprepared('DROP EVENT IF EXISTS actualizar_estados_cronograma_event');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_cronograma_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_cronograma_update');
    }
};
