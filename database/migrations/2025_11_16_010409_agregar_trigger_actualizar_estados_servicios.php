<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // TRIGGER INSERT
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_servicio_insert
            BEFORE INSERT ON Servicio
            FOR EACH ROW
            BEGIN
                IF NEW.estado = "Programado"
                AND CONCAT(NEW.fechaCrono, " ", NEW.horaCrono) <= NOW() THEN
                    SET NEW.estado = "EnProceso";
                END IF;
            END
        ');

        // TRIGGER UPDATE
        DB::unprepared('
            CREATE TRIGGER actualizar_estado_servicio_update
            BEFORE UPDATE ON Servicio
            FOR EACH ROW
            BEGIN
                IF NEW.estado = "Programado"
                AND CONCAT(NEW.fechaCrono, " ", NEW.horaCrono) <= NOW() THEN
                    SET NEW.estado = "EnProceso";
                END IF;
            END
        ');

        // EVENTO CADA MINUTO
        DB::unprepared('
            CREATE EVENT IF NOT EXISTS actualizar_estados_servicios_event
            ON SCHEDULE EVERY 1 MINUTE
            ON COMPLETION PRESERVE
            ENABLE
            DO
                UPDATE Servicio
                SET estado = "EnProceso"
                WHERE estado = "Programado"
                AND CONCAT(fechaCrono, " ", horaCrono) <= NOW();
        ');

        // Encender event scheduler
        DB::unprepared('SET GLOBAL event_scheduler = ON;');
    }

    public function down(): void
    {
        DB::unprepared('DROP EVENT IF EXISTS actualizar_estados_servicios_event');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_servicio_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS actualizar_estado_servicio_update');
    }
};
