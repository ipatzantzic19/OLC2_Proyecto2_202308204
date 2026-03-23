<script>
  import { executionHistory } from '../lib/store.js';
  import { editorCode } from '../lib/store.js';

  function loadExecution(execution) {
    editorCode.set(execution.code);
  }

  function formatDate(date) {
    return new Date(date).toLocaleString('es-ES', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: false,
    });
  }
</script>

<div class="history-container">
  {#if $executionHistory && $executionHistory.length > 0}
    <div class="history-list">
      {#each $executionHistory as execution, index (index)}
        <div class="history-item" class:success={execution.success} class:error={!execution.success}>
          <div class="history-header">
            <span class="timestamp">{formatDate(execution.timestamp)}</span>
            <span class="status">
              {execution.success ? '✅ Exitoso' : '❌ Error'}
            </span>
            <span class="time">({execution.executionTime}ms)</span>
          </div>
          <div class="history-preview">
            {execution.code.substring(0, 50)}...
          </div>
          <button class="btn-load" on:click={() => loadExecution(execution)}>
            Cargar
          </button>
        </div>
      {/each}
    </div>
  {:else}
    <p class="empty">No hay historial de ejecuciones aún.</p>
  {/if}
</div>

<style>
  .history-container {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
    background: #1e1e1e;
  }

  .history-container::-webkit-scrollbar {
    width: 8px;
  }

  .history-container::-webkit-scrollbar-track {
    background: #252526;
  }

  .history-container::-webkit-scrollbar-thumb {
    background: #464647;
    border-radius: 4px;
  }

  .history-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .history-item {
    padding: 10px;
    background: #252526;
    border: 1px solid #3e3e3e;
    border-radius: 4px;
    transition: all 0.3s;
  }

  .history-item:hover {
    background: #2a2a2b;
    border-color: #4a9eff;
  }

  .history-item.success {
    border-left: 3px solid #6a9955;
  }

  .history-item.error {
    border-left: 3px solid #f48771;
  }

  .history-header {
    display: flex;
    gap: 8px;
    margin-bottom: 6px;
    font-size: 11px;
  }

  .timestamp {
    color: #858585;
    flex-shrink: 0;
  }

  .status {
    color: #4a9eff;
    font-weight: 600;
  }

  .time {
    color: #888;
  }

  .history-preview {
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 11px;
    background: #1e1e1e;
    padding: 6px;
    border-radius: 2px;
    margin-bottom: 6px;
    max-height: 40px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .btn-load {
    width: 100%;
    padding: 6px;
    background: #3e3e3e;
    color: #e0e0e0;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
  }

  .btn-load:hover {
    background: #4a9eff;
    color: #fff;
  }

  .empty {
    text-align: center;
    color: #666;
    padding: 20px;
    margin: 0;
  }
</style>
