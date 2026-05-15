<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdpBorrador extends Model
{
    protected $table = 'cdp_borradores';

    protected $fillable = [
        'mesa_ayuda_requerimiento_id',
        'expediente_presupuestario_id',
        'cdp_id',
        'numero_cdp',
        'fecha_emision',
        'cf',
        'st',
        'gasto_operacional',
        'tipo_gasto',
        'nombre_iniciativa',
        'codigo_iniciativa',
        'cuenta_presupuestaria',
        'denominacion',
        'unidad_ejecutora',
        'numero_ue',
        'monto_impto_incluido',
        'validez',
        'caracter_gasto',
        'medio_solicitud',
        'numero_requerimiento',
        'ccosto_requirente',
        'moneda_compra',
        'fecha_paridad',
        'valor_paridad',
        'total_moneda_compra',
        'texto_certificacion',
        'notas',
        'respuesta_mesa_ayuda_borrador',
        'estado',
        'requiere_revision_usuario',
        'aprobado_por_usuario',
        'aprobado_por',
        'fecha_aprobacion',
        'json_generado',
        'datos_faltantes',
        'advertencias',
        'archivo_word',
        'archivo_pdf',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_paridad' => 'date',
        'gasto_operacional' => 'boolean',
        'monto_impto_incluido' => 'decimal:2',
        'valor_paridad' => 'decimal:4',
        'total_moneda_compra' => 'decimal:4',
        'notas' => 'array',
        'requiere_revision_usuario' => 'boolean',
        'aprobado_por_usuario' => 'boolean',
        'fecha_aprobacion' => 'datetime',
        'json_generado' => 'array',
        'datos_faltantes' => 'array',
        'advertencias' => 'array',
    ];

    /**
     * Campos requeridos para considerar un borrador completo antes de aprobar.
     * total_moneda_compra solo aplica si se seleccionó moneda de compra.
     */
    public static function calcularDatosFaltantes(array $datos): array
    {
        $requeridos = [
            'numero_cdp',
            'st',
            'cuenta_presupuestaria',
            'denominacion',
            'monto_impto_incluido',
            'moneda_compra',
        ];

        // total_moneda_compra es calculado automáticamente — no se exige al usuario

        return array_values(array_filter(
            $requeridos,
            fn ($campo) => $datos[$campo] === null || $datos[$campo] === ''
        ));
    }

    /**
     * Genera el siguiente número CDP en formato YYYY-NNN para el año actual.
     * Debe llamarse dentro de una transacción DB para evitar duplicados en concurrencia.
     */
    public static function siguienteNumeroCdp(): string
    {
        $year   = now()->year;
        $prefix = $year . '-';

        $maximo = static::where('numero_cdp', 'like', $prefix . '%')
            ->get(['numero_cdp'])
            ->map(fn ($b) => (int) substr($b->numero_cdp, strlen($prefix)))
            ->max() ?? 0;

        return $prefix . str_pad($maximo + 1, 3, '0', STR_PAD_LEFT);
    }

    public function requerimientoMesaAyuda()
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }

    public function expedientePresupuestario()
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_presupuestario_id');
    }

    public function cdp()
    {
        return $this->belongsTo(Cdp::class, 'cdp_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(Funcionario::class, 'aprobado_por', 'rut');
    }

    public function interacciones()
    {
        return $this->hasMany(AgenteInteraccion::class, 'cdp_borrador_id');
    }
}
