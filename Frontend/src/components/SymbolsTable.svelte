<script>
  export let symbolsData = [];
</script>

<div class="symbols-container">
  {#if symbolsData.length > 0}
    <table class="symbols-table">
      <thead>
        <tr>
          <th>Identificador</th><th>Tipo</th><th>Dimensiones</th>
          <th>Ámbito</th><th>Offset</th><th>Línea</th>
        </tr>
      </thead>
      <tbody>
        {#each symbolsData as sym}
          <tr>
            <td class="id">{sym.identifier}</td>
            <td class="type">{sym.type}</td>
            <td class="dims">{sym.scope === 'array' && sym.dims ? `[${sym.dims.join('][')}]` : '—'}</td>
            <td class="scope">{sym.scope || sym.function || '—'}</td>
            <td class="center muted">{sym.offset ?? '—'}</td>
            <td class="center muted">{sym.line ?? '—'}</td>
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
  .symbols-container { min-width: 640px; max-height: 420px; overflow-y: auto; }
  .symbols-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  thead { position: sticky; top: 0; background: #252526; }
  th { padding: 8px 10px; text-align: left; color: #858585; font-weight: 600;
       border-bottom: 1px solid #3E3E3E; }
  td { padding: 7px 10px; border-bottom: 1px solid #2A2A2A; }
  tbody tr:hover { background: #252526; }
  .center { text-align: center; }
  .muted  { color: #858585; }
  .id     { color: #4A9EFF; font-family: 'Consolas', monospace; }
  .type   { color: #6A9955; }
  .dims   { color: #CE9178; font-family: 'Consolas', monospace; }
  .scope  { color: #9CDCFE; }
  .empty  { display: flex; align-items: center; justify-content: center;
            padding: 40px 20px; color: #858585; font-size: 13px; }
</style>