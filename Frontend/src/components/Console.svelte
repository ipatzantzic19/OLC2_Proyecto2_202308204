<script>
  import { consoleOutput } from '../lib/store.js';

  // Auto-scroll al último elemento
  let container;
  $: if (container && $consoleOutput.length > 0) {
    setTimeout(() => {
      container.scrollTop = container.scrollHeight;
    }, 0);
  }
</script>

<div class="console" bind:this={container}>
  {#if $consoleOutput.length === 0}
    <div class="console-empty">
      <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#3E3E3E" stroke-width="1.5">
        <rect x="2" y="3" width="20" height="14" rx="2"/>
        <path d="M8 21h8M12 17v4"/>
        <path d="M7 8l3 3-3 3" stroke-width="2"/>
      </svg>
      <span>Presiona <strong>Compilar → ARM64</strong> para ver la salida aquí.</span>
    </div>
  {:else}
    {#each $consoleOutput as line}
      <div class="console-line {line.type}">
        {#if line.type === 'system'}
          <span class="prefix">›</span>
        {:else if line.type === 'error'}
          <span class="prefix err">✗</span>
        {:else if line.type === 'success'}
          <span class="prefix ok">✓</span>
        {:else if line.type === 'info'}
          <span class="prefix inf">i</span>
        {:else}
          <span class="prefix out">$</span>
        {/if}
        <span class="text">{line.message}</span>
      </div>
    {/each}
  {/if}
</div>

<style>
  .console {
    height: 100%;
    overflow-y: auto;
    background: #1E1E1E;
    padding: 10px 14px;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 12.5px;
    line-height: 1.6;
  }
  .console::-webkit-scrollbar { width: 8px; }
  .console::-webkit-scrollbar-track { background: #252526; }
  .console::-webkit-scrollbar-thumb { background: #464647; border-radius: 4px; }

  .console-empty {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: #555;
    font-size: 12px;
    text-align: center;
  }
  .console-empty strong { color: #4A9EFF; }

  .console-line {
    display: flex;
    gap: 8px;
    align-items: baseline;
    padding: 1px 0;
  }

  .prefix {
    flex-shrink: 0;
    width: 14px;
    text-align: center;
    font-size: 11px;
    color: #555;
  }
  .prefix.err { color: #F48771; }
  .prefix.ok  { color: #6A9955; }
  .prefix.inf { color: #4A9EFF; }
  .prefix.out { color: #858585; }

  .text { flex: 1; word-break: break-all; }

  .console-line.system  .text { color: #858585; font-style: italic; }
  .console-line.output  .text { color: #D4D4D4; }
  .console-line.error   .text { color: #F48771; }
  .console-line.success .text { color: #6A9955; }
  .console-line.info    .text { color: #4A9EFF; }
</style>