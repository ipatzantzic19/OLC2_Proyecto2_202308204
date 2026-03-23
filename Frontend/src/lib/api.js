const API_URL = import.meta.env.VITE_API_URL || '/api';

// ══════════════════════════════════════════════════════════════════════════════
//  COMPILADOR  (Proyecto 2)
// ══════════════════════════════════════════════════════════════════════════════

/** POST /api/compile → assembly ARM64 + errores + tabla de símbolos */
export async function compileCode(code) {
  try {
    const res = await fetch(`${API_URL}/compile`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code }),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch (e) {
    return {
      success: false, assembly: '',
      errors: [{ id: 1, type: 'Connection',
        description: `No se pudo conectar al backend: ${e.message}`,
        line: 0, column: 0 }],
      symbolTable: [], executionTime: '0ms', programOutput: '',
    };
  }
}

/** GET /api/compile-errors */
export async function fetchCompileErrors() {
  try {
    const res = await fetch(`${API_URL}/compile-errors`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch { return { success: false, errors: [], errorCount: 0 }; }
}

/** GET /api/compile-symbols */
export async function fetchCompileSymbols() {
  try {
    const res = await fetch(`${API_URL}/compile-symbols`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch { return { success: false, symbolTable: [], symbolCount: 0 }; }
}

/** GET /api/download-asm → abre descarga del .s */
export function downloadAsm() {
  const a = document.createElement('a');
  a.href = `${API_URL}/download-asm`;
  a.download = 'program.s';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}