<script>
  export let symbolsData = [];

  /**
   * Formatea el valor de un símbolo para presentación
   */
  function formatValue(value, type) {
    if (value === null || value === undefined) return '—';
    if (typeof value === 'boolean') return value ? 'true' : 'false';
    if (typeof value === 'string') return `"${value}"`;
    if (typeof value === 'number') return value.toString();
    return '—';
  }

  /**
   * Obtiene el tipo de símbolo para mostrar (con anotación de constante)
   */
  function formatType(sym) {
    let typeStr = sym.type || '—';
    if (sym.isConstant) {
      typeStr += ' (const)';
    }
    return typeStr;
  }
</script>

<div class="symbols-container">
  {#if symbolsData.length > 0}
    <table class="symbols-table">
      <thead>
        <tr>
          <th>Identificador</th>
          <th>Tipo</th>
          <th>Ámbito</th>
          <th>Valor</th>
          <th>Línea</th>
          <th>Columna</th>
        </tr>
      </thead>
      <tbody>
        {#each symbolsData as sym (sym.identifier + sym.line + sym.column)}
          <tr>
            <td class="id">{sym.identifier}</td>
            <td class="type">{formatType(sym)}</td>
            <td class="scope">{sym.scope || '—'}</td>
            <td class="value">{formatValue(sym.value, sym.type)}</td>
            <td class="center muted">{sym.line ?? '—'}</td>
            <td class="center muted">{sym.column ?? '—'}</td>
          </tr>
        {/each}
      </tbody>
    </table>
  {:else}
    <div class="empty">
      <p>Sin símbolos. Ejecuta o compila el código primero.</p>
    </div>
  {/if}
</div>

<style>
  .symbols-container { min-width: 700px; max-height: 420px; overflow-y: auto; }
  .symbols-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  thead { position: sticky; top: 0; background: #252526; }
  th { padding: 8px 10px; text-align: left; color: #858585; font-weight: 600;
       border-bottom: 1px solid #3E3E3E; }
  td { padding: 7px 10px; border-bottom: 1px solid #2A2A2A; }
  tbody tr:hover { background: #2D2D30; }
  .center { text-align: center; }
  .muted  { color: #858585; }
  .id     { color: #4A9EFF; font-family: 'Consolas', monospace; font-weight: 500; }
  .type   { color: #6A9955; font-family: 'Consolas', monospace; }
  .scope  { color: #9CDCFE; font-family: 'Consolas', monospace; }
  .value  { color: #CE9178; font-family: 'Consolas', monospace; }
  .empty  { display: flex; align-items: center; justify-content: center;
            padding: 40px 20px; color: #858585; font-size: 13px; }
</style>