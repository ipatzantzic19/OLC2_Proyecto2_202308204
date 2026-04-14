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
	# var sum int32 (valor por defecto)
	mov x0, xzr                                # int32 default = 0
	str x0, [x29, #-8]
	# sum = expr
	mov x0, xzr                                # int32 literal 0
	str x0, [x29, #-8]                         # guardar int32
	# for init
	# i := expr (tipo inferido)
	mov x0, xzr                                # int32 literal 0
	str x0, [x29, #-16]                        # guardar int32 inferido
.for_start_0:
	# for condición
	ldr x0, [x29, #-16]                        # i (int32)
	sub sp, sp, #16                            # reservar slot temporal
	str x0, [sp]                               # x0 → stack temporal
	mov x0, #5
	ldr x1, [sp]                               # lhs ← stack
	add sp, sp, #16
	cmp x1, x0                                 # comparar lhs vs rhs
	cset x0, lt                                # bool resultado (<)
	cbz x0, .for_end_1                         # falso → salir del bucle
	# sum = expr
	ldr x0, [x29, #-8]                         # sum (int32)
	sub sp, sp, #16                            # reservar slot temporal
	str x0, [sp]                               # x0 → stack temporal
	ldr x0, [x29, #-16]                        # i (int32)
	ldr x1, [sp]                               # lhs ← stack
	add sp, sp, #16
	add x0, x1, x0
	str x0, [x29, #-8]                         # guardar int32
.for_post_2:
	# for post
	# i++
	ldr x0, [x29, #-16]                        # cargar i
	add x0, x0, #1                             # i + 1
	str x0, [x29, #-16]                        # guardar i++
	b .for_start_0                             # volver al test
.for_end_1:
	ldr x0, [x29, #-8]                         # sum (int32)
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