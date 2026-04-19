<script lang="ts">
  import "monaco-editor/min/vs/editor/editor.main.css";
  import { onMount } from "svelte";
  import { get } from "svelte/store";
  import * as monaco from "monaco-editor";
  import {
    editorCode, consoleOutput,
    assemblyCode, compileErrors, compileSymbols,
    isCompiling, compileSuccess,
  } from "../lib/store.js";
  import Console from "./Console.svelte";
  import AssemblyView from "./AssemblyView.svelte";
  import Modal from "./Modal.svelte";
  import ErrorsTable from "./ErrorsTable.svelte";
  import SymbolsTable from "./SymbolsTable.svelte";
  import { compileCode, fetchCompileErrors, fetchCompileSymbols, downloadAsm } from "../lib/api.js";

  let activeTab   = "console";
  let fileName    = "main.go";
    let openFiles   = [{name: "main.go", content: ""}];
    let activeFileIndex = 0;
  let compTime    = "0ms";
  let isModalOpen = false;
  let modalContent: "errors" | "symbols" | null = null;

  let monacoContainer: HTMLDivElement;
  let monacoEditor: monaco.editor.IStandaloneCodeEditor | null = null;
  let unsubCode: (() => void) | null = null;
  let completionDisposable: monaco.IDisposable | null = null;

  function registerCompletions(): monaco.IDisposable {
    return monaco.languages.registerCompletionItemProvider("go", {
      triggerCharacters: [".", " "],
      provideCompletionItems: (model, position) => {
        const word = model.getWordUntilPosition(position);
        const range = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber,
          startColumn: word.startColumn, endColumn: word.endColumn };
        const kw = ["func","var","const","if","else","for","switch","case","default",
          "break","continue","return","true","false","nil","int32","float32","bool","string","rune"]
          .map(label => ({ label, kind: monaco.languages.CompletionItemKind.Keyword, insertText: label, range }));
        const builtins: monaco.languages.CompletionItem[] = [
          { label:"fmt.Println", kind: monaco.languages.CompletionItemKind.Function,
            insertText:"fmt.Println(${1:valor})",
            insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range },
        ];
        const snippets: monaco.languages.CompletionItem[] = [
          { label:"main fn", kind: monaco.languages.CompletionItemKind.Snippet,
            insertText:"func main() {\n\t$0\n}",
            insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range },
          { label:"for loop", kind: monaco.languages.CompletionItemKind.Snippet,
            insertText:"for ${1:i} := 0; ${1:i} < ${2:n}; ${1:i}++ {\n\t$0\n}",
            insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range },
        ];
        return { suggestions: [...kw, ...builtins, ...snippets] };
      },
    });
  }

  onMount(() => {
    completionDisposable = registerCompletions();
    monacoEditor = monaco.editor.create(monacoContainer, {
      value: get(editorCode), language: "go", theme: "vs-dark",
      automaticLayout: true, minimap: { enabled: false },
      fontSize: 13, lineHeight: 21, tabSize: 4, insertSpaces: true, scrollBeyondLastLine: false,
    });
    monacoEditor.onDidChangeModelContent(() => {
      if (!monacoEditor) return;
      const val = monacoEditor.getValue();
      if (val !== get(editorCode)) editorCode.set(val);
    });
    unsubCode = editorCode.subscribe(val => {
      if (!monacoEditor) return;
      if (val !== monacoEditor.getValue()) monacoEditor.setValue(val);
    });
    return () => { completionDisposable?.dispose(); unsubCode?.(); monacoEditor?.dispose(); };
  });

  // Keep the active file content synced with the editor buffer.
  $: if (openFiles[activeFileIndex]) {
    const current = openFiles[activeFileIndex];
    if (current.content !== $editorCode) {
      openFiles = openFiles.map((f, idx) => idx === activeFileIndex ? { ...f, content: $editorCode } : f);
    }
  }

  $: if (monacoEditor) monacoEditor.updateOptions({ readOnly: $isCompiling });

  async function runCompile() {
    if ($isCompiling) return;
    isCompiling.set(true);
    compileSuccess.set(null);
    assemblyCode.set('');
    activeTab = "assembly";
    consoleOutput.set([{ type: 'system', message: '⚙  Compilando Golampi → ARM64...' }]);
    try {
      const result = await compileCode($editorCode);
      assemblyCode.set(result.assembly ?? '');
      compileErrors.set(result.errors ?? []);
      
      // Procesar tabla de símbolos: puede ser array o objeto asociativo
      let symbolsArray = [];
      const symbolTableData = result.symbolTable ?? {};
      
      if (Array.isArray(symbolTableData)) {
        // Si es array, usarlo directamente
        symbolsArray = symbolTableData;
      } else if (typeof symbolTableData === 'object') {
        // Si es objeto, convertir a array
        symbolsArray = Object.entries(symbolTableData)
          .map(([key, value]: [string, any]) => ({
            identifier: key,
            ...(value && typeof value === 'object' ? value : {}),
          } as any))
          .filter((sym: any) => sym.type !== undefined);
      }
      
      // Ordenar por línea, columna, y orden de declaración
      symbolsArray.sort((a: any, b: any) => {
        const lineCmp = (a.line ?? 0) - (b.line ?? 0);
        if (lineCmp !== 0) return lineCmp;
        
        const colCmp = (a.column ?? 0) - (b.column ?? 0);
        if (colCmp !== 0) return colCmp;
        
        return (a.order ?? 0) - (b.order ?? 0);
      });
      
      compileSymbols.set(symbolsArray);
      compileSuccess.set(result.success);
      compTime = result.executionTime ?? '0ms';
      if (result.success) {
        const lines = (result.assembly ?? '').split('\n').length;
        consoleOutput.update(l => [...l,
          { type: 'success', message: `✓ Compilación exitosa en ${result.executionTime} — ${lines} líneas ARM64.` },
          { type: 'info',    message: 'Para ejecutar:' },
          { type: 'code',    message: 'aarch64-linux-gnu-gcc -o prog program.s && qemu-aarch64 -L /usr/aarch64-linux-gnu ./prog' },
        ]);
      } else {
        const cnt = result.errors?.length ?? 0;
        consoleOutput.update(l => [...l, { type: 'error', message: `✗ ${cnt} error(es). Ver tabla de errores.` }]);
        (result.errors ?? []).forEach(e => {
          const pos = (e.line || e.column) ? ` (L${e.line}:${e.column})` : '';
          consoleOutput.update(l => [...l, { type: 'error', message: `  [${e.type}] ${e.description}${pos}` }]);
        });
      }
    } catch (e) {
      compileSuccess.set(false);
      consoleOutput.update(l => [...l, { type: 'error', message: e instanceof Error ? e.message : 'Error inesperado' }]);
    } finally { isCompiling.set(false); }
  }

  async function showErrors()  { const r = await fetchCompileErrors();  if (r?.success) compileErrors.set(r.errors ?? []);       modalContent = 'errors';  isModalOpen = true; }
  async function showSymbols() { 
    const r = await fetchCompileSymbols(); 
    if (r?.success) {
      const symbolTableObj = r.symbolTable ?? {};
      const symbolsArray = Object.entries(symbolTableObj).map(([key, value]: [string, any]) => ({
        identifier: key,
        ...(value && typeof value === 'object' ? value : {}),
      } as any)).filter((sym: any) => sym.type !== undefined);
      compileSymbols.set(symbolsArray);
    }
    modalContent = 'symbols'; 
    isModalOpen = true; 
  }

  function clearAll() { consoleOutput.set([]); assemblyCode.set(''); compileErrors.set([]); compileSymbols.set([]); compileSuccess.set(null); compTime = '0ms'; }
  function newFile()  {
    if (!confirm('¿Descartar cambios?')) return;
    const name = `main${openFiles.length + 1}.go`;
    const content = 'func main() {\n    \n}';
    openFiles = [...openFiles, { name, content }];
    activeFileIndex = openFiles.length - 1;
    fileName = name;
    editorCode.set(content);
    clearAll();
  }
  function loadFile() {
    const i = document.createElement('input'); i.type = 'file'; i.accept = '.go,.golampi';
    i.onchange = (e: any) => { const f = e.target.files?.[0]; if (!f) return; const r = new FileReader(); r.onload = (ev: any) => { switchToFile(f.name, ev.target.result); }; r.readAsText(f); };
    i.click();
  }

  function switchToFile(name: string, content: string) {
    const idx = openFiles.findIndex(f => f.name === name);
    if (idx !== -1) {
      openFiles = openFiles.map((f, i) => i === idx ? { ...f, content } : f);
      activeFileIndex = idx;
    } else {
      openFiles = [...openFiles, { name, content }];
      activeFileIndex = openFiles.length - 1;
    }
    fileName = name;
    editorCode.set(content);
  }

  function closeTab(idx: number) {
    if (openFiles.length === 1) return;
    const nextFiles = openFiles.filter((_, i) => i !== idx);
    let nextIndex = activeFileIndex;
    if (idx < activeFileIndex) nextIndex = activeFileIndex - 1;
    if (nextIndex >= nextFiles.length) nextIndex = nextFiles.length - 1;
    openFiles = nextFiles;
    activeFileIndex = Math.max(0, nextIndex);
    fileName = openFiles[activeFileIndex].name;
    editorCode.set(openFiles[activeFileIndex].content);
  }

  function selectTab(idx: number) {
    activeFileIndex = idx;
    fileName = openFiles[idx].name;
    editorCode.set(openFiles[idx].content);
  }
  function saveFile() { const b = new Blob([$editorCode], {type:'text/plain'}); const u = URL.createObjectURL(b); const a = document.createElement('a'); a.href=u; a.download=fileName; a.click(); URL.revokeObjectURL(u); }
</script>

<div class="ide">
  <header class="top-bar">
    <div class="bar-left">
      <div class="logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="18" height="18" rx="2" stroke="#4A9EFF" stroke-width="2"/>
          <polyline points="7 8 12 13 17 8" stroke="#4A9EFF" stroke-width="2" stroke-linecap="round"/>
          <line x1="7" y1="16" x2="17" y2="16" stroke="#4A9EFF" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span class="logo-name">Golampi<span class="logo-ide">Compiler</span></span>
        <span class="logo-badge">ARM64</span>
      </div>
      <div class="sep"></div>
      <button class="btn" on:click={newFile}>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
        Nuevo
      </button>
      <button class="btn" on:click={loadFile}>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
        Cargar
      </button>
      <button class="btn" on:click={saveFile}>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Guardar
      </button>
      <div class="sep"></div>
      <button class="btn btn-compile" on:click={runCompile} disabled={$isCompiling}>
        {#if $isCompiling}
          <span class="spinner"></span> Compilando...
        {:else}
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
Compilar
        {/if}
      </button>
      <button class="btn btn-danger" on:click={clearAll}>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
      </button>
    </div>
    <div class="bar-right">
      {#if $isCompiling}
        <span class="status compiling">⚙ Compilando…</span>
      {:else if $compileSuccess === true}
        <span class="status ok">✓ Compilado ({compTime})</span>
      {:else if $compileSuccess === false}
        <span class="status err">✗ Errores de compilación</span>
      {/if}
    </div>
  </header>

  <div class="workspace">
    <section class="editor-pane">
      <div class="pane-tabs">
        <div class="pane-tabs-list">
          {#each openFiles as file, idx}
            <div class="file-tab" class:active={activeFileIndex === idx} role="button" tabindex="0" on:click={() => selectTab(idx)} on:keydown={(e) => (e.key === 'Enter' || e.key === ' ') && selectTab(idx)}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
              <span>{file.name}</span>
              {#if openFiles.length > 1}
                <button class="close-btn" on:click|stopPropagation={() => closeTab(idx)} aria-label={`Cerrar ${file.name}`}>x</button>
              {/if}
            </div>
          {/each}
        </div>
        <div class="tabs-spacer"></div>
      </div>
      <div class="monaco-wrap" bind:this={monacoContainer}></div>
    </section>

    <section class="output-pane">
      <div class="pane-tabs">
        <button class="pane-tab" class:active={activeTab === 'console'} on:click={() => activeTab = 'console'}>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
          Consola
        </button>
        <button class="pane-tab" class:active={activeTab === 'assembly'} on:click={() => activeTab = 'assembly'}>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
          ARM64 Assembly
          {#if $assemblyCode}<span class="tab-ok">✓</span>{/if}
        </button>
      </div>
      <div class="output-content">
        {#if activeTab === 'console'}<Console />{:else}<AssemblyView />{/if}
      </div>
    </section>
  </div>

  <footer class="bottom-bar">
    <div class="bar-left">
      <span class="section-label">REPORTES</span>
      <button class="btn btn-report" on:click={showErrors}>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="13"/><circle cx="12" cy="17" r="1" fill="currentColor"/></svg>
        Errores
        {#if $compileErrors.length > 0}<span class="badge-n err">{$compileErrors.length}</span>{/if}
      </button>
      <button class="btn btn-report" on:click={showSymbols}>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="8" y1="4" x2="8" y2="20"/></svg>
        Símbolos
        {#if $compileSymbols.length > 0}<span class="badge-n ok">{$compileSymbols.length}</span>{/if}
      </button>
      <button class="btn btn-dl" on:click={downloadAsm} disabled={!$assemblyCode}>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Descargar .s
      </button>
    </div>
    <div class="bar-right">
      <span class="tech">PHP 8</span>
      <span class="tech">ANTLR4</span>
      <span class="tech arm">AArch64</span>
    </div>
  </footer>

  {#if isModalOpen}
    <Modal on:close={() => isModalOpen = false}>
      {#if modalContent === 'errors'}
        <h2 class="modal-title err">⚠ Tabla de Errores</h2>
        <ErrorsTable errorsData={$compileErrors} />
      {:else if modalContent === 'symbols'}
        <h2 class="modal-title sym">⊞ Tabla de Símbolos</h2>
        <SymbolsTable symbolsData={$compileSymbols} />
      {/if}
    </Modal>
  {/if}
</div>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
.ide { display: flex; flex-direction: column; height: 100vh; background: #1E1E1E; color: #D4D4D4; font-family: 'Segoe UI', system-ui, sans-serif; }
.top-bar { display: flex; justify-content: space-between; align-items: center; height: 70px; padding: 10px 16px; gap: 10px; background: #2D2D2D; border-bottom: 1px solid #3E3E3E; flex-shrink: 0; }
.bar-left { display: flex; align-items: center; gap: 10px; }
.bar-right { display: flex; align-items: center; gap: 8px; }
.logo { display: flex; align-items: center; gap: 10px; padding-right: 12px; }
.logo-name { font-size: 17px; font-weight: 700; color: #E0E0E0; }
.logo-ide { color: #4A9EFF; }
.logo-badge { padding: 4px 8px; background: rgba(197,134,192,.2); border: 1px solid rgba(197,134,192,.4); border-radius: 4px; font-size: 11px; font-weight: 700; color: #C586C0; }
.sep { width: 1px; height: 30px; background: #3E3E3E; margin: 0 8px; }
.btn { display: flex; align-items: center; gap: 7px; padding: 8px 14px; background: transparent; border: 1px solid #3E3E3E; border-radius: 4px; color: #D4D4D4; font-size: 14px; cursor: pointer; transition: all .15s; white-space: nowrap; }
.btn:hover:not(:disabled) { background: #3E3E3E; border-color: #555; }
.btn:disabled { opacity: .4; cursor: not-allowed; }
.btn-compile { background: #4A9EFF; border-color: #4A9EFF; color: #fff; font-weight: 700; font-size: 15px; padding: 9px 18px; }
.btn-compile:hover:not(:disabled) { background: #3A8EDF; border-color: #3A8EDF; }
.btn-danger { border-color: #F48771; color: #F48771; }
.btn-danger:hover:not(:disabled) { background: rgba(244,135,113,.1); }
.btn-report { color: #C586C0; border-color: #C586C0; }
.btn-report:hover:not(:disabled) { background: rgba(197,134,192,.1); }
.btn-dl { color: #6A9955; border-color: #6A9955; }
.btn-dl:hover:not(:disabled) { background: rgba(106,153,85,.1); }
.spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid currentColor; border-top-color: transparent; border-radius: 50%; animation: spin .7s linear infinite; margin-right: 4px; }
@keyframes spin { to { transform: rotate(360deg); } }
.status { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.status.ok       { background: rgba(106,153,85,.2);  color: #6A9955; }
.status.err      { background: rgba(244,135,113,.2); color: #F48771; }
.status.compiling{ background: rgba(197,134,192,.2); color: #C586C0; }
.workspace { display: flex; flex: 1; overflow: hidden; }
.editor-pane { flex: 1; display: flex; flex-direction: column; border-right: 1px solid #3E3E3E; min-width: 0; }
.output-pane { width: 48%; display: flex; flex-direction: column; min-width: 0; }
.pane-tabs { display: flex; background: #252526; border-bottom: 1px solid #3E3E3E; flex-shrink: 0; gap: 2px; padding: 6px 8px; }
.pane-tabs-list { display: flex; gap: 2px; overflow-x: auto; }
.file-tab { display: flex; align-items: center; gap: 6px; padding: 7px 12px; background: #3E3E3E; border: 1px solid #3E3E3E; border-radius: 4px 4px 0 0; color: #858585; cursor: pointer; transition: all .15s; white-space: nowrap; font-size: 12px; font-weight: 500; }
.file-tab:hover { background: #464647; }
.file-tab.active { background: #252526; border-color: #4A9EFF; color: #4A9EFF; border-bottom: none; }
.close-btn { background: none; border: none; color: inherit; cursor: pointer; padding: 0 4px; font-size: 12px; transition: color .15s; margin-left: 4px; }
.close-btn:hover { color: #FFF; }
.tabs-spacer { flex: 1; }
.pane-tab { display: flex; align-items: center; gap: 6px; padding: 7px 14px; font-size: 12px; font-weight: 600; color: #858585; background: transparent; border: none; border-bottom: 2px solid transparent; cursor: pointer; transition: all .15s; }
.pane-tab:hover { color: #D4D4D4; }
.pane-tab.active { color: #4A9EFF; border-bottom-color: #4A9EFF; }
.tab-ok { padding: 1px 5px; background: rgba(106,153,85,.25); color: #6A9955; border-radius: 3px; font-size: 10px; }
.monaco-wrap { flex: 1; min-height: 0; }
.output-content { flex: 1; overflow: hidden; }
.bottom-bar { display: flex; justify-content: space-between; align-items: center; padding: 6px 14px; background: #252526; border-top: 1px solid #3E3E3E; flex-shrink: 0; }
.section-label { font-size: 10px; font-weight: 700; color: #555; letter-spacing: .07em; padding-right: 6px; }
.badge-n { padding: 1px 5px; border-radius: 3px; font-size: 10px; font-weight: 700; }
.badge-n.err { background: rgba(244,135,113,.25); color: #F48771; }
.badge-n.ok  { background: rgba(106,153,85,.25);  color: #6A9955; }
.tech { padding: 3px 7px; background: #3E3E3E; border-radius: 3px; font-size: 11px; color: #858585; }
.tech.arm { color: #C586C0; border: 1px solid rgba(197,134,192,.3); }
.modal-title { font-size: 16px; font-weight: 700; margin-bottom: 14px; }
.modal-title.err { color: #F48771; }
.modal-title.sym { color: #4A9EFF; }
</style>