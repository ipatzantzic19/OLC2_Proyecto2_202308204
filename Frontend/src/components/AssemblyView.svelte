<script>
  import { assemblyCode, compileSuccess } from '../lib/store.js';

  // Simple ARM64 syntax highlight via regex
  function highlightLine(line) {
    if (!line) return '';

    // Comment lines
    if (/^\s*(\/\/|#)/.test(line)) {
      return `<span class="asm-comment">${esc(line)}</span>`;
    }
    // Labels (ending with colon, no leading whitespace)
    if (/^[a-zA-Z_.][a-zA-Z0-9_.]*:/.test(line)) {
      const m = line.match(/^([a-zA-Z_.][a-zA-Z0-9_.]*:)(.*)/);
      if (m) return `<span class="asm-label">${esc(m[1])}</span><span class="asm-rest">${highlightRest(m[2])}</span>`;
    }
    // Directives (.section, .global, .align, .asciz, .string)
    if (/^\s*\./.test(line)) {
      return `<span class="asm-directive">${esc(line)}</span>`;
    }
    // Instructions
    return highlightRest(line);
  }

  function highlightRest(line) {
    // Split on first comment
    // Only // is considered an inline comment (# within [...] is valid immediates/operands)
    const ciIdx = line.indexOf('//');
    let instr = line;
    let comment = '';

    if (ciIdx > 0) { instr = line.slice(0, ciIdx); comment = line.slice(ciIdx); }

    // Highlight instruction mnemonic (first word)
    const mnemonicRe = /^(\s*)([a-z][a-z0-9._]*)/;
    const match = instr.match(mnemonicRe);
    let result = '';
    if (match) {
      const ops = instr.slice(match[0].length);
      result = `${esc(match[1])}<span class="asm-mnemonic">${esc(match[2])}</span>${highlightOps(ops)}`;
    } else {
      result = esc(instr);
    }

    if (comment) result += `<span class="asm-comment">${esc(comment)}</span>`;
    return result;
  }

  function highlightOps(ops) {
    // Escape first to prevent HTML injection
    let escaped = esc(ops);
    
    // Highlight registers - no escape needed for captured group since it's only alphanumeric
    escaped = escaped.replace(
      /\b(x[0-9]{1,2}|w[0-9]{1,2}|sp|fp|lr|xzr|wzr|d[0-9]{1,2}|s[0-9]{1,2})\b/g,
      '<span class="asm-register">$1</span>'
    );
    
    // Highlight immediates
    escaped = escaped.replace(
      /(#-?[0-9]+)/g,
      '<span class="asm-immediate">$1</span>'
    );
    
    // Highlight strings (already escaped in 'escaped' variable)
    escaped = escaped.replace(
      /(&quot;[^&]*&quot;)/g,
      '<span class="asm-string">$1</span>'
    );
    
    return escaped;
  }

  function esc(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  $: lines = $assemblyCode ? $assemblyCode.split('\n') : [];
</script>

<div class="asm-container">
  {#if $assemblyCode}
    <div class="asm-toolbar">
      <span class="asm-info">
        {lines.length} líneas · ARM64 (AArch64)
        {#if $compileSuccess === true}
          <span class="badge success">✓ compilado</span>
        {:else if $compileSuccess === false}
          <span class="badge error">⚠ con errores</span>
        {/if}
      </span>
    </div>
    <div class="asm-code">
      {#each lines as line, i}
        <div class="asm-line">
          <span class="asm-gutter">{i + 1}</span>
          <span class="asm-text">{@html highlightLine(line)}</span>
        </div>
      {/each}
    </div>
  {:else}
    <div class="asm-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4A9EFF" stroke-width="1.5">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <path d="M8 8h8M8 12h8M8 16h5"/>
      </svg>
      <p>Presiona <strong>Compile → ARM64</strong> para generar el código ensamblador.</p>
      <p class="asm-hint">El código generado puede ensamblarse con <code>aarch64-linux-gnu-gcc</code><br>y ejecutarse con <code>qemu-aarch64</code>.</p>
    </div>
  {/if}
</div>

<style>
  .asm-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #1E1E1E;
    overflow: hidden;
  }

  .asm-toolbar {
    padding: 6px 14px;
    background: #252526;
    border-bottom: 1px solid #3E3E3E;
    font-size: 11px;
    color: #858585;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }

  .badge {
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
  }
  .badge.success { background: rgba(106,153,85,.25); color: #6A9955; }
  .badge.error   { background: rgba(244,135,113,.2); color: #F48771; }

  .asm-code {
    flex: 1;
    overflow-y: auto;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 12.5px;
    line-height: 1.55;
    padding: 8px 0;
  }

  .asm-code::-webkit-scrollbar { width: 10px; }
  .asm-code::-webkit-scrollbar-track { background: #252526; }
  .asm-code::-webkit-scrollbar-thumb { background: #464647; border-radius: 5px; }

  .asm-line {
    display: flex;
    min-width: 0;
  }
  .asm-line:hover { background: #2a2d2e; }

  .asm-gutter {
    flex-shrink: 0;
    width: 42px;
    text-align: right;
    padding-right: 12px;
    color: #495057;
    user-select: none;
    font-size: 11px;
    line-height: 1.55;
  }

  .asm-text {
    flex: 1;
    white-space: pre;
    color: #D4D4D4;
    padding-right: 20px;
  }

  /* Syntax colors */
  :global(.asm-comment)   { color: #6A9955; font-style: italic; }
  :global(.asm-label)     { color: #DCDCAA; font-weight: bold; }
  :global(.asm-directive) { color: #C586C0; }
  :global(.asm-mnemonic)  { color: #569CD6; }
  :global(.asm-register)  { color: #9CDCFE; }
  :global(.asm-immediate) { color: #B5CEA8; }
  :global(.asm-string)    { color: #CE9178; }
  :global(.asm-rest)      { color: #D4D4D4; }

  .asm-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    gap: 14px;
    color: #858585;
    text-align: center;
    padding: 20px;
  }

  .asm-empty p { font-size: 13px; line-height: 1.5; }
  .asm-empty strong { color: #4A9EFF; }
  .asm-hint { font-size: 11.5px; color: #555; }
  .asm-hint code {
    background: #2d2d2d;
    padding: 1px 5px;
    border-radius: 3px;
    color: #9CDCFE;
  }
</style>