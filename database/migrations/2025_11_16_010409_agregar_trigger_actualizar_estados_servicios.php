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
        // Crear trigger que actualiza automáticamente el estado de Programado a EnProceso
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_servicio_insert
            BEFORE INSERT ON Servicio
            FOR EACH ROW
            BEGIN
                -- Si se está insertando un servicio Programado y ya pasó su fecha/hora
                IF NEW.estado = "Programado" AND
                   CONCAT(NEW.fechaSol, " ", NEW.horaSol) <= NOW() THEN
                    SET NEW.estado = "EnProceso";
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER actualizar_estado_servicio_update
            BEFORE UPDATE ON Servicio
            FOR EACH ROW
            BEGIN
                -- Si el servicio está en Programado y ya pasó su fecha/hora, cambiar a EnProceso
                IF NEW.estado = "Programado" AND
                   CONCAT(NEW.fechaSol, " ", NEW.horaSol) <= NOW() THEN
                    SET NEW.estado = "EnProceso";
                END IF;
            END
        ');

        // Crear evento que se ejecuta cada minuto para actualizar servicios existentes
        DB::unprepared('
            CREATE EVENT IF NOT EXISTS actualizar_estados_servicios_event
            ON SCHEDULE EVERY 1 MINUTE
            DO
            BEGIN
                UPDATE Servicio
                SET estado = "EnProceso"
                WHERE estado = "Programado"
                AND CONCAT(fechaSol, " ", horaSol) <= NOW();
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
        DB::unprepared('DROP EVENT IF EXISTS actualizar_estados_servicios_event');

        // Eliminar los triggers
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_servicio_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_servicio_update');
    }
};
