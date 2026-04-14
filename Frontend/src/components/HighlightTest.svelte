<!-- Test de Highlighting del Assembly -->
<script>
  // Test de la función highlightOps
  
  function esc(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function highlightOps(ops) {
    // Escape first to prevent HTML injection
    let escaped = esc(ops);
    
    // Highlight registers
    escaped = escaped.replace(
      /\b(x[0-9]{1,2}|w[0-9]{1,2}|sp|fp|lr|xzr|wzr|d[0-9]{1,2}|s[0-9]{1,2})\b/g,
      '<span class="asm-register">$1</span>'
    );
    
    // Highlight immediates
    escaped = escaped.replace(
      /(#-?[0-9]+)/g,
      '<span class="asm-immediate">$1</span>'
    );
    
    return escaped;
  }

  function highlightLine(line) {
    let instr = line.trim();
    let comment = '';
    
    const comIdx = instr.indexOf('//');
    if (comIdx > 0) { 
      instr = line.slice(0, comIdx); 
      comment = line.slice(comIdx); 
    }

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

  // Test cases
  const testLines = [
    'stp x29, x30, [sp, #-16]!                  // guardar fp',
    'mov x29, sp                                // establecer frame pointer',
    'sub sp, sp, #32                            // reservar 32 bytes',
    'ldr s0, [x9, :lo12:.flt_0]                 // cargar float32',
    'fsub s1, s1, s0                            // float32 resta optimizada',
  ];

  let results = [];
  
  testLines.forEach(line => {
    const highlighted = highlightLine(line);
    results.push({
      original: line,
      highlighted: highlighted
    });
  });
</script>

<style>
  .test-container {
    font-family: 'Monaco', 'Courier New', monospace;
    padding: 20px;
    background: #1e1e1e;
    color: #d4d4d4;
  }
  
  .test-item {
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #3e3e42;
    border-radius: 4px;
  }
  
  .original {
    color: #888;
    font-size: 11px;
    margin-bottom: 5px;
  }
  
  .highlighted {
    font-size: 13px;
  }
  
  :global(.asm-mnemonic) { color: #569cd6; font-weight: bold; }
  :global(.asm-register) { color: #9cdcfe; }
  :global(.asm-immediate) { color: #b5cea8; }
  :global(.asm-comment) { color: #6a9955; }
</style>

<div class="test-container">
  <h3>Test de Syntax Highlighting - Assembly Viewer</h3>
  <p>Verificando que los tags HTML sean correctos sin mostrar caracteres raros</p>
  
  {#each results as result}
    <div class="test-item">
      <div class="original">Original: {result.original}</div>
      <div class="highlighted">{@html result.highlighted}</div>
    </div>
  {/each}
</div>
