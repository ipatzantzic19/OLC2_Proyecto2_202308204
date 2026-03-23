<script>
  export let errorsData = [];
</script>

<div class="errors-container">
  {#if errorsData.length > 0}
    <table class="errors-table">
      <thead>
        <tr>
          <th>#</th><th>Tipo</th><th>Descripción</th><th>Línea</th><th>Columna</th>
        </tr>
      </thead>
      <tbody>
        {#each errorsData as error, i}
          <tr class="error-row">
            <td class="center muted">{error.id ?? i + 1}</td>
            <td class="type">{error.type}</td>
            <td class="desc">{error.description}</td>
            <td class="center muted">{error.line}</td>
            <td class="center muted">{error.column}</td>
          </tr>
        {/each}
      </tbody>
    </table>
  {:else}
    <div class="empty">
      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#6A9955" stroke-width="1.5">
        <circle cx="12" cy="12" r="10"/>
        <path d="M8 12l3 3 5-6"/>
      </svg>
      <p>Sin errores — ¡el código es válido!</p>
    </div>
  {/if}
</div>

<style>
  .errors-container { min-width: 560px; max-height: 420px; overflow-y: auto; }
  .errors-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  thead { position: sticky; top: 0; background: #252526; }
  th { padding: 8px 10px; text-align: left; color: #858585; font-weight: 600;
       border-bottom: 1px solid #3E3E3E; }
  td { padding: 7px 10px; border-bottom: 1px solid #2A2A2A; }
  .error-row:hover { background: #252526; }
  .center { text-align: center; }
  .muted  { color: #858585; }
  .type   { color: #F48771; font-weight: 600; min-width: 90px; }
  .desc   { color: #D4D4D4; }
  .empty  { display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 40px 20px; gap: 12px;
            color: #6A9955; font-size: 13px; }
</style>