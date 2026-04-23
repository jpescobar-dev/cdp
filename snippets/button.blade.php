<form action="{{ route('contractual.revisiones.analizar', $revision) }}" method="POST">
@csrf
<button class="btn btn-success btn-sm">Analizar con IA</button>
</form>
