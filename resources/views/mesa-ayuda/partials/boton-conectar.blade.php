<form method="POST" action="{{ route('mesa-ayuda.extracciones.ejecutar') }}" class="d-inline">
    @csrf
    <input type="hidden" name="modo" value="sincrono">
    <input type="hidden" name="max_folios" value="0">

    <button type="submit" class="btn btn-primary btn-sm"
            onclick="return confirm('Se conectará a Mesa de Ayuda en modo solo lectura y se extraerán los requerimientos pendientes. ¿Continuar?')">
        <i class="fa-solid fa-plug mr-1"></i>
        Conectar y extraer
    </button>
</form>

<form method="POST" action="{{ route('mesa-ayuda.extracciones.ejecutar') }}" class="d-inline ml-1">
    @csrf
    <input type="hidden" name="modo" value="sincrono">
    <input type="hidden" name="max_folios" value="1">

    <button type="submit" class="btn btn-outline-secondary btn-sm"
            onclick="return confirm('Se extraerá solo un folio para prueba controlada. ¿Continuar?')">
        <i class="fa-solid fa-vial mr-1"></i>
        Probar 1 folio
    </button>
</form>
