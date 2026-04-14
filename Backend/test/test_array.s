# ============================================================
# Golampi Compiler — Fase 2 — ARM64 (AArch64)
# Compilar:
#   aarch64-linux-gnu-gcc -o programa program.s -lc
# Ejecutar:
#   qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa
# ============================================================

.section .data
.str_0: .string "%d"
.str_1: .string "\n"

.section .text
.global main


main:
	# ── función main ── registro de activación ──
	stp x29, x30, [sp, #-16]!                  # guardar fp (enlace control) y lr
	mov x29, sp                                # establecer frame pointer
	sub sp, sp, #16                            # reservar 16 bytes (locales + params)
	# var arr array (valor por defecto)
	mov x0, xzr                                # array default = 0
	str x0, [x29, #-8]
	mov x0, xzr                                # array access — pendiente Fase 3
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	mov x0, xzr                                # array access — pendiente Fase 3
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
	mov x0, xzr                                # array access — pendiente Fase 3
	# fmt.Println arg 0 (int32)
	mov x1, x0                                 # int32 → x1 para printf
	adrp x0, .str_0
	add x0, x0, :lo12:.str_0
	bl printf
	adrp x0, .str_1
	add x0, x0, :lo12:.str_1
	bl printf
.epilogue_main:
	# ── epílogo main ──
	add sp, sp, #16                            # liberar espacio de locales
	ldp x29, x30, [sp], #16                    # restaurar fp y lr
	mov x0, #0                                 # exit code 0
	ret