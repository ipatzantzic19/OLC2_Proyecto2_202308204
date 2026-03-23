<script>
  import { createEventDispatcher } from 'svelte';
  const dispatch = createEventDispatcher();

  function handleKey(e) {
    if (e.key === 'Escape') dispatch('close');
  }
  function backdrop(e) {
    if (e.target === e.currentTarget) dispatch('close');
  }
</script>

<svelte:window on:keydown={handleKey} />

<!-- svelte-ignore a11y-click-events-have-key-events -->
<div class="overlay" on:click={backdrop}>
  <div class="modal">
    <button class="close-btn" on:click={() => dispatch('close')}>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
    <div class="content">
      <slot />
    </div>
  </div>
</div>

<style>
  .overlay {
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, .65);
    display: flex; align-items: center; justify-content: center;
    z-index: 1000;
    animation: fadeIn .15s ease;
  }
  @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }

  .modal {
    position: relative;
    background: #252526;
    border: 1px solid #3E3E3E;
    border-radius: 8px;
    padding: 24px;
    max-width: 90vw;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    animation: slideUp .18s ease;
    min-width: 460px;
  }
  @keyframes slideUp { from { transform: translateY(12px); opacity: 0 } to { transform: none; opacity: 1 } }

  .modal::-webkit-scrollbar { width: 8px; }
  .modal::-webkit-scrollbar-track { background: #2A2A2A; }
  .modal::-webkit-scrollbar-thumb { background: #464647; border-radius: 4px; }

  .close-btn {
    position: absolute; top: 12px; right: 12px;
    background: transparent; border: none;
    color: #858585; cursor: pointer; padding: 4px;
    border-radius: 4px; display: flex; align-items: center; justify-content: center;
    transition: all .15s;
  }
  .close-btn:hover { background: #3E3E3E; color: #D4D4D4; }

  .content { color: #D4D4D4; font-family: 'Segoe UI', system-ui, sans-serif; font-size: 13px; }
</style>