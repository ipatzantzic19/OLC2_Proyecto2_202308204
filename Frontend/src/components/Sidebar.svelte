<script>
  import { onMount } from 'svelte';
  import { editorCode, clearConsole } from '../lib/store.js';
  import GolampiAPI from '../lib/api.js';

  let languageInfo = {};
  let loading = true;

  const loadLanguageInfo = async () => {
    try {
      const result = await GolampiAPI.getLanguageInfo();
      if (result.success !== false) {
        languageInfo = result.info || result;
      }
    } catch (error) {
      console.error('Error loading language info:', error);
    } finally {
      loading = false;
    }
  };

  // Cargar info del lenguaje al montar
  onMount(() => {
    loadLanguageInfo();
  });

  function loadTemplate(name) {
    const templates = {
      helloWorld: `package main

Func main() {
  var message string = "¡Hola Mundo!";
  println(message);
}`,
      variables: `package main

Func main() {
  var x int32 := 10;
  var y int32 := 20;
  var suma int32 := x + y;
  println("Suma:", suma);
}`,
      loop: `package main

Func main() {
  for i := 0; i < 5; i++ {
    println("Iteración:", i);
  }
}`,
      function: `package main

Func main() {
  var resultado int32 = sumar(5, 3);
  println("Resultado:", resultado);
}

Func sumar(a int32, b int32) int32 {
  return a + b;
}`,
    };

    editorCode.set(templates[name]);
    clearConsole();
  }
</script>

<div class="sidebar">
  <div class="section">
    <h3>📚 Plantillas</h3>
    <div class="template-buttons">
      <button class="template-btn" on:click={() => loadTemplate('helloWorld')}>
        Hola Mundo
      </button>
      <button class="template-btn" on:click={() => loadTemplate('variables')}>
        Variables
      </button>
      <button class="template-btn" on:click={() => loadTemplate('loop')}>
        Bucles
      </button>
      <button class="template-btn" on:click={() => loadTemplate('function')}>
        Funciones
      </button>
    </div>
  </div>

  {#if !loading && languageInfo.types}
    <div class="section">
      <h3>🔤 Tipos de Datos</h3>
      <ul class="type-list">
        {#each languageInfo.types as type}
          <li>{type}</li>
        {/each}
      </ul>
    </div>

    <div class="section">
      <h3>⚙️ Características</h3>
      <ul class="feature-list">
        {#each languageInfo.features as feature}
          <li>{feature}</li>
        {/each}
      </ul>
    </div>
  {/if}

  <div class="section info">
    <p><strong>Golampi {languageInfo.version}</strong></p>
    <p>{languageInfo.description}</p>
  </div>
</div>

<style>
  .sidebar {
    width: 250px;
    background: #2d2d2d;
    border-right: 1px solid #3e3e3e;
    overflow-y: auto;
    padding: 16px;
    font-size: 13px;
  }

  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-track {
    background: #252526;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: #464647;
    border-radius: 3px;
  }

  .section {
    margin-bottom: 20px;
  }

  .section h3 {
    margin: 0 0 10px 0;
    color: #4a9eff;
    font-size: 13px;
    font-weight: 600;
  }

  .template-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
  }

  .template-btn {
    padding: 8px;
    background: #3e3e3e;
    color: #d4d4d4;
    border: 1px solid #3e3e3e;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.3s;
  }

  .template-btn:hover {
    background: #4a9eff;
    color: #fff;
    border-color: #4a9eff;
  }

  .type-list,
  .feature-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .type-list li,
  .feature-list li {
    padding: 4px 0;
    color: #d4d4d4;
    border-bottom: 1px solid #3e3e3e;
  }

  .type-list li:before {
    content: "▪ ";
    color: #6a9955;
    margin-right: 4px;
  }

  .feature-list li:before {
    content: "✓ ";
    color: #4a9eff;
    margin-right: 4px;
  }

  .info {
    background: #252526;
    padding: 10px;
    border-radius: 3px;
    border-left: 3px solid #4a9eff;
  }

  .info p {
    margin: 4px 0;
    color: #888;
    font-size: 11px;
  }
</style>
