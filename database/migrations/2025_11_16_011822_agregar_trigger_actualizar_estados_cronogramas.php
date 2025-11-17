<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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

        // Crear trigger para INSERT
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_cronograma_insert
            BEFORE INSERT ON CronogramaAtencion
            FOR EACH ROW
            BEGIN
                -- Si la fecha es menor que hoy, marcar como inactivoPasado
                IF NEW.fechaCrono < CURDATE() THEN
                    SET NEW.estado = "inactivoPasado";
                -- Si la fecha es hoy, marcar como activo
                ELSEIF NEW.fechaCrono = CURDATE() THEN
                    SET NEW.estado = "activo";
                -- Si la fecha es futura, marcar como inactivoFut
                ELSEIF NEW.fechaCrono > CURDATE() THEN
                    SET NEW.estado = "inactivoFut";
                END IF;
            END
        ');

        // Crear trigger para UPDATE
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_cronograma_update
            BEFORE UPDATE ON CronogramaAtencion
            FOR EACH ROW
            BEGIN
                -- Si la fecha es menor que hoy, marcar como inactivoPasado
                IF NEW.fechaCrono < CURDATE() THEN
                    SET NEW.estado = "inactivoPasado";
                -- Si la fecha es hoy, marcar como activo
                ELSEIF NEW.fechaCrono = CURDATE() THEN
                    SET NEW.estado = "activo";
                -- Si la fecha es futura, marcar como inactivoFut
                ELSEIF NEW.fechaCrono > CURDATE() THEN
                    SET NEW.estado = "inactivoFut";
                END IF;
            END
        ');

        // Crear evento que se ejecuta cada día a las 00:01
        DB::unprepared('
            CREATE EVENT IF NOT EXISTS actualizar_estados_cronograma_event
            ON SCHEDULE EVERY 1 DAY
            STARTS CONCAT(CURDATE() + INTERVAL 1 DAY, " 00:01:00")
            DO
            BEGIN
                -- Marcar cronogramas pasados como inactivoPasado
                UPDATE CronogramaAtencion
                SET estado = "inactivoPasado"
                WHERE fechaCrono < CURDATE()
                AND estado != "inactivoPasado";

                -- Marcar cronograma de hoy como activo
                UPDATE CronogramaAtencion
                SET estado = "activo"
                WHERE fechaCrono = CURDATE()
                AND estado = "inactivoFut";
            END
        ');

        // Asegurarse de que el event scheduler esté habilitado
        DB::unprepared('SET GLOBAL event_scheduler = ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el evento
        DB::unprepared('DROP EVENT IF EXISTS actualizar_estados_cronograma_event');

        // Eliminar los triggers
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_cronograma_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_cronograma_update');
    }
};
