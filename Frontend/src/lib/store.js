import { writable } from 'svelte/store';

// ── Editor ───────────────────────────────────────────────────────────────────
export const editorCode = writable(
`func main() {
    a := 8
    b := 7
    var resultado int32
    if a > b {
        resultado = a
    } else {
        resultado = b
    }
    fmt.Println(resultado)
}`
);

// ── Compilador ───────────────────────────────────────────────────────────────
export const assemblyCode   = writable('');    // ARM64 generado
export const compileErrors  = writable([]);    // errores de compilación
export const compileSymbols = writable([]);    // tabla de símbolos
export const isCompiling    = writable(false);
export const compileSuccess = writable(null);  // null | true | false
export const consoleOutput  = writable([]);    // log/mensajes de la consola