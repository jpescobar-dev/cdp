<form action="{{ route('contractual.revisiones.analizar', $revision) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit"
            class="btn btn-outline-success btn-sm"
            onclick="return confirm('¿Deseas ejecutar el análisis de esta revisión?');">
        Analizar con IA
    </button>
</form>
